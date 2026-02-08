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
        [$app, $action] = $this->parseCamelCasePair($name);

        if (!$this->assertAppExists($app)) {
            $this->components->error("App {$app} does not exist.");
            return self::FAILURE;
        }

        $className = Str::studly($action);
        $urlKey = Str::snake($action);
        $slug = Str::kebab($action);
        $phpActionNamespace = "{$phpPrefix}\\{$app}\\Actions";
        $phpHandlerNamespace = "{$phpPrefix}\\{$app}\\Actions\\Handler";
        $classDir = base_path("ui5/{$app}/src/Actions");

        $classPath = "{$classDir}/{$className}Action.php";
        if (File::exists($classPath)) {
            $this->components->error("Action class already exists: {$classPath}");
            return self::FAILURE;
        }

        File::ensureDirectoryExists("{$classDir}/Handler");

        $this->files->put($classPath, $this->compileStub('Ui5Action.stub', [
            'phpActionNamespace' => $phpActionNamespace,
            'phpHandlerNamespace' => $phpHandlerNamespace,
            'ui5Namespace' => implode('.', [$jsPrefix, Str::snake($app), 'actions', $urlKey]),
            'className' => $className,
            'title' => Str::headline($className),
            'description' => "Action for " . Str::headline($className),
            'slug' => $slug,
            'method' => trim($this->option('method'))
        ]));
        $this->files->put("{$classDir}/Handler/{$className}Handler.php", $this->compileStub('ActionHandler.stub', [
            'phpHandlerNamespace' => $phpHandlerNamespace,
            'className' => $className,
        ]));

        $this->components->success("Action created: {$classPath}");
        $this->components->info("💡 Don’t forget to register this action in your module");

        return self::SUCCESS;
    }
}
