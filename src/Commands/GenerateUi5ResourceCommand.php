<?php

namespace LaravelUi5\Core\Commands;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateUi5ResourceCommand extends BaseGenerator
{
    protected $signature = 'ui5:resource
        {name : Resource name in App/Resource format (e.g. Offers/Header)}';

    protected $description = 'Generates a new Ui5 Resource class using a predefined stub.';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle(): int
    {
        $name = $this->argument('name');
        [$app, $resource] = $this->parseCamelCasePair($name);

        if (!$this->assertAppExists($app)) {
            $this->components->error("App {$app} does not exist.");
            return self::FAILURE;
        }

        $className = Str::studly($resource);
        $urlKey = Str::snake($resource);
        $slug = Str::kebab($resource);
        $phpPrefix = $this->getPhpNamespacePrefix();
        $jsPrefix = $this->getJsNamespacePrefix();
        $phpNamespace = "{$phpPrefix}\\Resources";
        $classDir = base_path("ui5/{$app}/src/Resources");

        $classPath = "{$classDir}/{$className}Resource.php";
        if (File::exists($classPath)) {
            $this->components->error("Resource class already exists: {$classPath}");
            return self::FAILURE;
        }

        File::ensureDirectoryExists("{$classDir}/Provider");

        $this->files->put($classPath, $this->compileStub('Ui5Resource.stub', [
            'phpNamespace' => $phpNamespace,
            'ui5Namespace' => $jsPrefix . '.resources.' . $urlKey,
            'className' => $className,
            'title' => Str::headline($className),
            'description' => "Resource for " . Str::headline($className),
            'slug' => $slug,
        ]));
        $this->files->put("{$classDir}/Provider/{$className}Provider.php", $this->compileStub('ResourceProvider.stub', [
            'phpNamespace' => $phpNamespace,
            'className' => "{$className}Provider",
        ]));

        $this->components->success("Resource created: {$classPath}");
        $this->components->info("💡 Don’t forget to register this resource in your module");

        return self::SUCCESS;
    }
}
