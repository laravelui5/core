<?php

namespace LaravelUi5\Core;

use Flat3\Lodata\Controller\Response;
use Flat3\Lodata\Endpoint;
use Flat3\Lodata\Helper\DBAL;
use Flat3\Lodata\Helper\Filesystem;
use Flat3\Lodata\Helper\Flysystem;
use Flat3\Lodata\Helper\Symfony;
use Flat3\Lodata\Interfaces\ServiceEndpointInterface;
use Flat3\Lodata\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use LaravelUi5\Core\Commands\GenerateSelfContainedUi5AppCommand;
use LaravelUi5\Core\Commands\GenerateUi5Action;
use LaravelUi5\Core\Commands\GenerateUi5AppCommand;
use LaravelUi5\Core\Commands\GenerateUi5CardCommand;
use LaravelUi5\Core\Commands\GenerateUi5Dashboard;
use LaravelUi5\Core\Commands\GenerateUi5LibraryCommand;
use LaravelUi5\Core\Commands\GenerateUi5ReportCommand;
use LaravelUi5\Core\Commands\GenerateUi5ResourceCommand;
use LaravelUi5\Core\Commands\GenerateUi5TileCommand;
use LaravelUi5\Core\Contracts\ParameterResolverInterface;
use LaravelUi5\Core\Contracts\SettingResolverInterface;
use LaravelUi5\Core\Controllers\ODataController;
use LaravelUi5\Core\Infrastructure\Contracts\Ui5SourceOverrideStoreInterface;
use LaravelUi5\Core\Infrastructure\Contracts\Ui5SourceStrategyResolverInterface;
use LaravelUi5\Core\Infrastructure\NoSourceStrategy;
use LaravelUi5\Core\Infrastructure\Ui5SourceOverrideStore;
use LaravelUi5\Core\Infrastructure\Ui5SourceStrategyResolver;
use LaravelUi5\Core\Services\ParameterResolver;
use LaravelUi5\Core\Services\SettingResolver;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5RegistryInterface;
use LaravelUi5\Core\Ui5\Ui5Registry;
use LaravelUi5\Core\View\Components\Ui5Element;
use RuntimeException;

class Ui5CoreServiceProvider extends ServiceProvider
{
    public const string UI5_ROUTE_PREFIX = 'ui5';

    /**
     * Middleware stack for the current SYSTEM environment.
     * Set in register() via assertSystemMiddleware().
     */
    public array $systemMiddleware = [];

    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateSelfContainedUi5AppCommand::class,
                GenerateUi5Action::class,
                GenerateUi5AppCommand::class,
                GenerateUi5CardCommand::class,
                GenerateUi5Dashboard::class,
                GenerateUi5LibraryCommand::class,
                GenerateUi5ReportCommand::class,
                GenerateUi5TileCommand::class,
                GenerateUi5ResourceCommand::class
            ]);
        }

        $this->app->singleton(Ui5SourceOverrideStoreInterface::class, Ui5SourceOverrideStore::class);
        $this->app->singleton(Ui5SourceStrategyResolverInterface::class, Ui5SourceStrategyResolver::class);
        $this->app->singleton(
            Ui5RegistryInterface::class,
            config('ui5.registry', Ui5Registry::class)
        );
        $this->app->singleton(ParameterResolverInterface::class, ParameterResolver::class);
        $this->app->singleton(SettingResolverInterface::class, SettingResolver::class);

        $this->app->singleton('ui5.artifact.resolvers', function () {
            return collect(config('ui5.artifact_resolvers'))
                ->map(fn ($class) => app($class))
                ->all();
        });

        $this->mergeConfigFrom(__DIR__.'/../config.php', 'ui5');
    }

    public function boot(): void
    {
        $this->overrideLodataConfig();
        $this->assertSystemMiddleware();

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config.php' => config_path('ui5.php')], 'ui5-config');
        }

        Route::prefix(self::UI5_ROUTE_PREFIX)
            ->middleware($this->systemMiddleware)
            ->group(__DIR__ . '/../routes/ui5.php');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'ui5');

        Blade::component('element', Ui5Element::class, 'ui5');
        Blade::directive('IncludeIfSdk', function (string $expression): string {
            return <<<PHP
<?php
try {
    \$__ui5_context = app(\\LaravelUi5\\Core\\Contracts\\Ui5ContextInterface::class);
    \$__ui5_app = \$__ui5_context->artifact();
    if (
        \$__ui5_app instanceof \\LaravelUi5\\Core\\Ui5\\Contracts\\Ui5AppInterface
        && \$__ui5_app->getLaravelUiManifest() instanceof \\LaravelUi5\\Core\\Ui5\\Capabilities\\Ui5ShellFragmentInterface
    ) {
        if (\$__env->exists({$expression})) echo \$__env->make({$expression}, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render();
    }
}
catch(\\Illuminate\\Contracts\\Container\\BindingResolutionException \$e) {
    // ignored if no Ui5Context bound
}
?>
PHP;
        });

        // Let’s examine the request path
        $segments = explode('/', request()->path());

        // we only kick off operation when path prefix is configured in lodata.php
        // and bypass all other routes for performance
        if ($segments[0] === config('lodata.prefix')) {

            $service = $this->resolveEndpointFromRequest($segments, config('lodata.endpoints', []));

            $this->bootServices($service);
        }
    }

    private function overrideLodataConfig(): void
    {
        $lodataConfig = config('ui5.lodata', []);

        // OpenUI5 requires this to be '4.0'
        Arr::set($lodataConfig, 'version', '4.0');

        // Merge endpoints and modules
        $configuredEndpoints = Arr::get($lodataConfig, 'endpoints', []);
        $ui5Modules = config('ui5.modules', []);
        foreach ($ui5Modules as $segment => $moduleClass) {
            if (!array_key_exists($segment, $configuredEndpoints)) {
                $configuredEndpoints[$segment] = $moduleClass;
            }
        }
        Arr::set($lodataConfig, 'endpoints', $configuredEndpoints);

        // Override original Lodata config
        Config::set('lodata', $lodataConfig);
    }

    protected function assertSystemMiddleware(): void
    {
        $system = env('SYSTEM', 'PRO');
        $middleware = config("ui5.systems.{$system}.middleware");

        if (!is_array($middleware) || empty($middleware)) {
            throw new RuntimeException("Missing middleware configuration for SYSTEM environment: '{$system}' in config/ui5.php");
        }

        $this->systemMiddleware = $middleware;
    }

    private function resolveEndpointFromRequest(array $segments, array $uris): Endpoint
    {
        if (empty($uris) || count($segments) === 1) {
            return new Endpoint('');
        }

        $key = $segments[1] ?? null;

        if (isset($uris[$key])) {
            $clazz = $uris[$key];

            if (!class_exists($clazz)) {
                throw new RuntimeException("Endpoint/Ui5Module class `{$clazz}` does not exist");
            }

            if (is_subclass_of($clazz, Ui5ModuleInterface::class)) {
                $module = new $clazz($key, new NoSourceStrategy());
                $app = $module->getApp();
                if (is_subclass_of($app, Endpoint::class)) {
                    return $app;
                }

                $classString = get_class($app);
                throw new RuntimeException("Class `{$classString}` must extend Flat3\\Lodata\\Endpoint");
            }

            if (is_subclass_of($clazz, ServiceEndpointInterface::class)) {
                return new $clazz($key);
            }

            throw new RuntimeException("Class `{$clazz}` must implement ServiceEndpointInterface");
        }

        throw new RuntimeException("Endpoint class for URI segment `{$key}` does not exist");
    }

    private function bootServices(Endpoint $service): void
    {
        $this->app->instance(Endpoint::class, $service);

        $this->app->bind(DBAL::class, fn($app, array $args) => new DBAL\DBAL4($args['connection']));

        $this->loadJsonTranslationsFrom(__DIR__.'/../lang');

        // next instantiate and discover the global Model
        $model = $service->discover(new Model());
        assert($model instanceof Model);

        // and register it with the container
        $this->app->instance(Model::class, $model);
        $this->app->alias(Model::class, 'lodata.model');

        $this->app->bind(Response::class, fn() => new Symfony\Response6());

        $this->app->bind(Filesystem::class, fn() => new Flysystem\Flysystem3());

        $route = $service->route();

        Route::any("{$route}{path}", ODataController::class)
            ->where('path', '(.*)')
            ->middleware($this->systemMiddleware);
    }
}
