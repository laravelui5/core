<?php

namespace LaravelUi5\Core\Commands;

use Exception;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateSelfContainedUi5AppCommand extends BaseGenerator
{

    protected $signature = 'ui5:sca
        {name : The name of the ui5 app}
        {--package-prefix=pragmatiqu : The composer package namespace prefix}
        {--php-ns-prefix=Pragmatiqu : Root namespace prefix for PHP classes}
        {--js-ns-prefix=io.pragmatiqu : Root namespace prefix for JS artifacts}
        {--title= : The title of the app}
        {--description= : The description of the app}
        {--vendor="Pragmatiqu IT GmbH" : The vendor of the module}';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * @throws Exception
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $this->assertCamelCase('Ui5App', $name);

        $phpPrefix = rtrim($this->option('php-ns-prefix'), '\\');
        $jsPrefix = rtrim($this->option('js-ns-prefix'), '.');
        $packagePrefix = rtrim($this->option('package-prefix'), '/');
        $urlKey = Str::snake($name);
        $ui5Namespace = implode('.', [$jsPrefix, $urlKey]);
        $phpNamespace = "{$phpPrefix}\\{$name}";
        $className = "{$name}App";
        $moduleName = "{$name}Module";
        $title = $this->option('title') ?? $name;
        $description = $this->option('description') ?? 'Ui5App generated via ui5:sca';

        $targetPath = base_path("ui5/{$name}");

        $targetFile = "{$targetPath}/src/{$className}.php";
        if (File::exists($targetFile)) {
            $this->components->error("Ui5App {$name} already exists.");
            return self::FAILURE;
        }

        File::ensureDirectoryExists("{$targetPath}/src/");

        $this->files->put("{$targetPath}/src/{$moduleName}.php", $this->compileStub('Ui5ModuleApp.stub', [
            'phpNamespace' => $phpNamespace,
            'class' => $className,
            'moduleClass' => $moduleName,
        ]));

        // Create Ui5App
        $this->files->put($targetFile, $this->compileStub('Ui5AppSelfContained.stub', [
            'namespace' => $phpNamespace,
            'class' => $className,
            'name' => $name,
            'ui5Namespace' => $ui5Namespace,
            'urlKey' => $urlKey,
            'title' => $title,
            'description' => $description,
            'component' => str_replace('.', '/', $ui5Namespace)
        ]));

        // Manifest
        $this->files->put("{$targetPath}/src/{$name}Manifest.php", $this->compileStub('Ui5Manifest.stub', [
            'phpNamespace' => $phpNamespace,
            'class' => $name
        ]));

        // composer.json
        $this->files->put("{$targetPath}/composer.json", $this->compileStub('composer.stub', [
            'packagePrefix' => $packagePrefix,
            'urlKey' => $urlKey,
            'description' => $description,
            'namespace' => json_encode($phpNamespace),
        ]));

        // ServiceProvider
        $this->files->put("{$targetPath}/src/{$name}ServiceProvider.php", $this->compileStub('ServiceProvider.stub', [
            'namespace' => $phpNamespace,
            'name' => $name,
        ]));

        File::ensureDirectoryExists("{$targetPath}/resources/app/controller");
        File::ensureDirectoryExists("{$targetPath}/resources/app/i18n");
        File::ensureDirectoryExists("{$targetPath}/resources/app/view");

        $this->files->put("{$targetPath}/resources/app/manifest.json", $this->compileStub('manifest.stub', [
            'ui5Namespace' => $ui5Namespace,
            'title' => $title,
            'description' => $description,
            'version' => '1.0.0',
        ]));
        $this->files->put("{$targetPath}/resources/app/Component.js", $this->compileStub('Component.stub', [
            'ui5Namespace' => $ui5Namespace,
        ]));
        $this->files->put("{$targetPath}/resources/app/controller/App.controller.js", $this->compileStub('App.controller.stub', [
            'ui5Namespace' => $ui5Namespace,
        ]));
        $this->files->put("{$targetPath}/resources/app/view/App.view.xml", $this->compileStub('App.view.stub', [
            'ui5Namespace' => $ui5Namespace,
        ]));
        $this->files->put("{$targetPath}/resources/app/i18n/i18n.properties", $this->compileStub('i18n.stub', [
            'title' => $title,
            'description' => $description,
        ]));
        $this->files->put("{$targetPath}/resources/app/i18n/i18n_en.properties", $this->compileStub('i18n.stub', [
            'title' => $title,
            'description' => $description,
        ]));

        $this->components->success("UI5App '$name' created successfully.");
        return self::SUCCESS;
    }
}
