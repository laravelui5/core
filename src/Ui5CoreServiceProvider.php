<?php

namespace LaravelUi5\Core;

use Flat3\Lodata\Controller\Response;
use Flat3\Lodata\Helper\DBAL;
use Flat3\Lodata\Helper\Filesystem;
use Flat3\Lodata\Helper\Flysystem;
use Flat3\Lodata\Helper\Symfony;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Blade;
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
use LaravelUi5\Core\Contracts\ExecutableInvokerInterface;
use LaravelUi5\Core\Contracts\ParameterResolverInterface;
use LaravelUi5\Core\Contracts\SettingResolverInterface;
use LaravelUi5\Core\Infrastructure\Contracts\Ui5SourceOverrideStoreInterface;
use LaravelUi5\Core\Infrastructure\Contracts\Ui5SourceStrategyResolverInterface;
use LaravelUi5\Core\Infrastructure\Ui5SourceOverrideStore;
use LaravelUi5\Core\Infrastructure\Ui5SourceStrategyResolver;
use LaravelUi5\Core\Middleware\FetchCsrfToken;
use LaravelUi5\Core\Middleware\ODataAuthGate;
use LaravelUi5\Core\Middleware\ResolveODataEndpoint;
use LaravelUi5\Core\Middleware\ResolveUi5Context;
use LaravelUi5\Core\Middleware\Ui5AuthGate;
use LaravelUi5\Core\Services\ExecutableInvoker;
use LaravelUi5\Core\Services\ParameterResolver;
use LaravelUi5\Core\Services\SettingResolver;
use LaravelUi5\Core\Ui5\Contracts\Ui5RegistryInterface;
use LaravelUi5\Core\Ui5\Ui5InfrastructureCollector;
use LaravelUi5\Core\Ui5\Ui5Registry;
use LaravelUi5\Core\View\Components\Ui5Element;

class Ui5CoreServiceProvider extends ServiceProvider
{
    public const string UI5_ROUTE_PREFIX = 'ui5';

    public const string ODATA_ROUTE_PREFIX = 'odata';

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
        $this->app->singleton(Ui5RegistryInterface::class, config('ui5.registry', Ui5Registry::class));
        $this->app->singleton(ParameterResolverInterface::class, ParameterResolver::class);
        $this->app->singleton(SettingResolverInterface::class, SettingResolver::class);
        $this->app->singleton(ExecutableInvokerInterface::class, ExecutableInvoker::class);
        $this->app->singleton(Ui5InfrastructureCollector::class);

        $this->app->singleton('ui5.artifact.resolvers', function () {
            return collect(config('ui5.artifact_resolvers'))
                ->map(fn($class) => app($class))
                ->all();
        });

        $this->app->bind(DBAL::class, fn($app, array $args) => new DBAL\DBAL4($args['connection']));
        $this->app->bind(Response::class, fn() => new Symfony\Response6());
        $this->app->bind(Filesystem::class, fn() => new Flysystem\Flysystem3());

        $this->mergeConfigFrom(__DIR__ . '/../config.php', 'ui5');
    }

    /**
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config.php' => config_path('ui5.php')], 'ui5-config');
        }

        $this->app->make(Ui5InfrastructureCollector::class)
            ->add(CoreModule::class)
            ->add(DashboardModule::class)
            ->add(ReportModule::class);

        Route::prefix(self::UI5_ROUTE_PREFIX)
            ->middleware([
                'web',
                ResolveUi5Context::class,
                Ui5AuthGate::class
            ])->group(__DIR__ . '/../routes/ui5.php');

        Route::prefix(self::ODATA_ROUTE_PREFIX)
            ->middleware([
                'web',
                FetchCsrfToken::class,
                ResolveODataEndpoint::class,
                ODataAuthGate::class,
            ])->group(__DIR__ . '/../routes/odata.php');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'ui5');

        $this->loadJsonTranslationsFrom(base_path('vendor/pragmatiqu/lodata-modular/lang'));

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
    }
}
