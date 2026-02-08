<?php

namespace LaravelUi5\Core\Commands;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionException;

class GenerateUi5Action extends BaseGenerator
{
    protected $signature = 'ui5:action
        {name : Action name in App/Action format (e.g. Offers/ToggleLock)}
        {--method=POST : The HTTP method to use for this action}';

    protected $description = 'Generates a new Ui5 Action class using a predefined stub.';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * @throws ReflectionException
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        [$app, $action] = $this->parseCamelCasePair($name);

        if (!$this->assertAppExists($app)) {
            $this->components->error("App {$app} does not exist.");
            return self::FAILURE;
        }

        $className = Str::studly($action);
        $urlKey = Str::snake($action);
        $slug = Str::kebab($action);
        $phpPrefix = $this->getPhpNamespacePrefix($app);
        $jsPrefix = $this->getJsNamespacePrefix($app);
        $phpActionNamespace = "{$phpPrefix}\\Actions";
        $phpHandlerNamespace = "{$phpPrefix}\\Actions\\Handler";
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
            'ui5Namespace' => $jsPrefix . '.actions.' . $urlKey,
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
