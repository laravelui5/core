<?php

namespace LaravelUi5\Core\Commands;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateUi5Resource extends BaseGenerator
{
    protected $signature = 'ui5:resource
        {name : Resource name in App/Resource format (e.g. Offers/Header)}
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
        [$app, $resource] = $this->parseCamelCasePair($name);

        if (!$this->assertAppExists($app)) {
            $this->components->error("App {$app} does not exist.");
            return self::FAILURE;
        }

        $className = Str::studly($resource);
        $urlKey = Str::snake($resource);
        $slug = Str::kebab($resource);
        $phpNamespace = "{$phpPrefix}\\{$app}\\Resources\\{$className}";
        $classDir = base_path("ui5/{$app}/src/Resources/{$className}");

        $classPath = "{$classDir}/Resource.php";
        if (File::exists($classPath)) {
            $this->components->error("Resource class already exists: {$classPath}");
            return self::FAILURE;
        }

        File::ensureDirectoryExists($classDir);

        $this->files->put($classPath, $this->compileStub('Ui5Resource.stub', [
            'phpNamespace' => $phpNamespace,
            'ui5Namespace' => implode('.', [$jsPrefix, Str::snake($app), 'resources', $urlKey]),
            'title' => Str::headline($className),
            'description' => "Resource for " . Str::headline($className),
            'slug' => $slug,
        ]));
        $this->files->put("{$classDir}/Provider.php", $this->compileStub('ResourceProvider.stub', [
            'phpNamespace' => $phpNamespace,
        ]));

        $this->components->success("Resource created: {$classPath}");
        $this->components->info("💡 Don’t forget to register this resource in your module");

        return self::SUCCESS;
    }
}
