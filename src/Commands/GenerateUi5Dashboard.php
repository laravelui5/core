<?php

namespace LaravelUi5\Core\Commands;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateUi5Dashboard extends BaseGenerator
{
    protected $signature = 'ui5:dashboard
        {name : Dashboard name in App/Dashboard format (e.g. Offers/ProjectKpi)}';

    protected $description = 'Generates a new Ui5 Dashboard class using a predefined stub.';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle(): int
    {
        $name = $this->argument('name');
        [$app, $dashboard] = $this->parseCamelCasePair($name);

        if (!$this->assertAppExists($app)) {
            $this->components->error("App {$app} does not exist.");
            return self::FAILURE;
        }

        $phpPrefix = $this->getPhpNamespacePrefix();
        $jsPrefix = $this->getJsNamespacePrefix();

        $className = Str::studly($dashboard);
        $urlKey = Str::snake($dashboard);
        $namespace = "{$phpPrefix}\\Dashboards";
        $classPath = base_path("ui5/{$app}/src/Dashboards");
        $bladePath = base_path("ui5/{$app}/resources/ui5/dashboards");

        $filePath = "{$classPath}/{$className}.php";
        if (File::exists($filePath)) {
            $this->components->error("Dashboard class already exists: {$filePath}");
            return self::FAILURE;
        }

        $resourcePath = "{$bladePath}/{$urlKey}.blade.php";
        if (File::exists($resourcePath)) {
            $this->components->success("Dashboard template already exists: {$resourcePath}");
            return self::FAILURE;
        }

        File::ensureDirectoryExists($classPath);
        File::ensureDirectoryExists($bladePath);

        $this->files->put($filePath, $this->compileStub('Ui5Dashboard.stub', [
            'namespace' => $namespace,
            'class' => $className,
            'ui5Namespace' => $jsPrefix . '.dashboards.' . Str::kebab($dashboard),
            'title' => Str::headline($className),
            'description' => "Dashboard for " . Str::headline($className),
            'url_key' => Str::kebab($dashboard),
            'path' => "/../../resources/ui5/dashboards/{$urlKey}.blade.php",
        ]));

        $this->files->put($resourcePath, $this->compileStub('Dashboard.blade.stub', []));

        $this->components->success("Dashboard `{$filePath}` successfully created.");
        $this->components->info("💡 Don’t forget to register this dashboard in `config/ui5.php`");
        return self::SUCCESS;
    }
}
