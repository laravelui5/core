<?php

namespace LaravelUi5\Core\Commands;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateUi5Action extends BaseGenerator
{
    protected $signature = 'ui5:action
        {name : Action name in App/Action format (e.g. Offers/ToggleLock)}
        {--method=POST : The HTTP method to use for this action}
        {--with-params : Allow uri encoded parameters}
        {--php-ns-prefix=Pragmatiqu : Root namespace prefix for PHP classes}
        {--js-ns-prefix=io.pragmatiqu : Root namespace prefix for JS artifacts}';

    protected $description = 'Generates a new Ui5 Action class using a predefined stub.';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle(): int
    {
        $name = $this->argument('name');
        $phpPrefix = rtrim($this->option('php-ns-prefix'), '\\');
        $jsPrefix = rtrim($this->option('js-ns-prefix'), '.');
        $withParams = $this->option('with-params') ?? false;
        [$app, $action] = $this->parseCamelCasePair($name);

        if (!$this->assertAppExists($app)) {
            $this->components->error("App {$app} does not exist.");
            return self::FAILURE;
        }

        $className = Str::studly($action);
        $urlKey = Str::snake($action);
        $slug = Str::kebab($action);
        $phpNamespace = "{$phpPrefix}\\{$app}\\Actions\\{$className}";
        $classDir = base_path("ui5/{$app}/src/Actions/{$className}");
        $variant = $withParams ? 'extends AbstractUi5Action' : 'implements Ui5ActionInterface';
        $useVariant = $withParams ? 'LaravelUi5\\Core\\Ui5\\AbstractUi5Action' : 'LaravelUi5\\Core\\Ui5\\Contracts\\Ui5ActionInterface';

        $classPath = "{$classDir}/Action.php";
        if (File::exists($classPath)) {
            $this->components->error("Action class already exists: {$classPath}");
            return self::FAILURE;
        }

        File::ensureDirectoryExists($classDir);

        $this->files->put($classPath, $this->compileStub('Ui5Action.stub', [
            'phpNamespace' => $phpNamespace,
            'ui5Namespace' => implode('.', [$jsPrefix, Str::snake($app), 'actions', $urlKey]),
            'title' => Str::headline($className),
            'description' => "Action for " . Str::headline($className),
            'slug' => $slug,
            'variant' => $variant,
            'useVariant' => $useVariant,
            'method' => trim($this->option('method'))
        ]));
        $this->files->put("{$classDir}/Handler.php", $this->compileStub('ActionHandler.stub', [
            'phpNamespace' => $phpNamespace,
        ]));

        $this->components->success("Action created: {$classPath}");
        $this->components->info("💡 Don’t forget to register this action in your module");

        return self::SUCCESS;
    }
}
