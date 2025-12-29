<?php

namespace LaravelUi5\Core\Commands;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use JsonException;
use LaravelUi5\Core\Commands\Concerns\RunsUi5Build;
use LaravelUi5\Core\Commands\Concerns\WritesSourceOverride;
use LaravelUi5\Core\Introspection\App\Ui5AppSource;
use LogicException;

class GenerateUi5AppCommand extends BaseGenerator
{
    use RunsUi5Build;
    use WritesSourceOverride;

    protected $signature = 'ui5:app {name : The name of the ui5 app}
                            {--package-prefix=pragmatiqu : The composer package namespace prefix}
                            {--php-ns-prefix=Pragmatiqu : The namespace prefix for the php package}
                            {--js-ns-prefix=io.pragmatiqu : The JS namespace prefix}
                            {--create : Create a new module from scratch}
                            {--refresh : Overwrite existing files without confirmation}
                            {--vendor=Pragmatiqu IT GmbH : The vendor of the module}
                            {--auto-build : Run UI5 build before importing assets}';

    protected $description = 'Generate a Ui5App module from a UI5 frontend project';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * @throws JsonException
     */
    public function handle(): int
    {
        $appName = $this->argument('name');
        $this->assertCamelCase('Ui5App', $appName);

        $jsNamespacePrefix = rtrim($this->option('js-ns-prefix'), '.');
        $ui5AppFolderName = Str::kebab($appName);

        $conventionPaths = [
            base_path("../ui5-{$ui5AppFolderName}/"),
            base_path("../{$jsNamespacePrefix}.{$ui5AppFolderName}/"),
        ];

        /** @var string $sourcePath */
        $sourcePath = collect($conventionPaths)
            ->first(fn($path) => File::exists($path));

        if (is_null($sourcePath)) {
            throw new LogicException(
                sprintf(
                    'Source folder for UI5 app not found. Tried:\n - %s',
                    implode("\n - ", $conventionPaths)
                )
            );
        }

        $targetPath = base_path("ui5/{$appName}/src/");
        $className = "{$appName}App";
        $moduleClassName = "{$appName}Module";
        $phpNamespacePrefix = rtrim($this->option('php-ns-prefix'), '\\');
        $phpNamespace = "{$phpNamespacePrefix}\\{$appName}";
        $targetFile = "{$targetPath}{$className}.php";
        $targetModuleFile = "{$targetPath}{$moduleClassName}.php";
        $vendor = trim($this->option('vendor'));

        if ($this->output->isVerbose()) {
            $this->info("sourcePath: {$sourcePath}");
            $this->info("targetPath: {$targetPath}");
            $this->info("className: {$className}");
            $this->info("moduleClassName: {$moduleClassName}");
            $this->info("phpNamespace: {$phpNamespace}");
            $this->info("targetFile: {$targetFile}");
            $this->info("targetModuleFile: {$targetModuleFile}");
        }

        $create = $this->option('create');
        $refresh = $this->option('refresh');
        $exists = File::exists($targetFile);

        // Decision tree
        if ($create && $exists) {
            $this->components->error("Module already exists. Use --refresh to update.");
            return self::FAILURE;
        }

        if ($refresh && !$exists) {
            $this->components->error("Module does not exist. Use --create to scaffold.");
            return self::FAILURE;
        }

        if (!$create && !$refresh) {
            if ($exists) {
                $this->components->info("Module already exists. Use --refresh to update.");
            } else {
                $this->components->info("Module does not exist. Use --create to scaffold.");
            }
            return self::FAILURE;
        }

        if ($this->option('auto-build')) {
            $this->runBuild($sourcePath);
        }

        File::ensureDirectoryExists($targetPath);

        $source = Ui5AppSource::fromWorkspace(
            path: $sourcePath,
            vendor: $vendor,
            isDev: true
        );

        if (!$exists) {
            // composer.json
            $this->files->put("{$targetPath}../composer.json", $this->compileStub('composer.stub', [
                'packagePrefix' => $this->option('package-prefix'),
                'urlKey' => $ui5AppFolderName,
                'description' => $source->getDescriptor()->getDescription(),
                'namespace' => json_encode($phpNamespace),
            ]));

            // ServiceProvider
            $this->files->put("{$targetPath}/{$appName}ServiceProvider.php", $this->compileStub('ServiceProvider.stub', [
                'namespace' => $phpNamespace,
                'name' => $appName,
            ]));

            // Module
            $this->files->put("{$targetPath}/{$appName}Module.php", $this->compileStub('Ui5ModuleApp.stub', [
                'phpNamespace' => $phpNamespace,
                'class' => $className,
                'moduleClass' => $moduleClassName,
                'name' => json_encode($appName),
            ]));

            // Manifest
            $this->files->put("{$targetPath}/{$appName}Manifest.php", $this->compileStub('Ui5Manifest.stub', [
                'phpNamespace' => $phpNamespace,
                'class' => $appName
            ]));
        }

        // app
        $this->files->put($targetFile, $this->compileStub('Ui5App.stub', [
            'name' => $appName,
            'namespace' => $phpNamespace,
            'class' => $className,
            'ui5Namespace' => $source->getDescriptor()->getNamespace(),
            'appVersion' => $source->getDescriptor()->getVersion(),
            'title' => addslashes($source->getDescriptor()->getTitle()),
            'description' => addslashes($source->getDescriptor()->getDescription()),
            'bootstrapAttributes' => var_export($source->getBootstrap()->getAttributes(), true),
            'resourceNamespaces' => var_export($source->getBootstrap()->getResourceNamespaces(), true),
            'inlineScript' => $source->getBootstrap()->getInlineScript(),
            'inlineCss' => $source->getBootstrap()->getInlineCss(),
            'vendor' => $vendor,
        ]));

        // dist assets
        $i18nFiles = collect(File::files($sourcePath . 'dist/i18n'))
            ->filter(fn($f) => Str::endsWith($f->getFilename(), '.properties'))
            ->map(fn($f) => 'i18n/' . $f->getFilename())
            ->all();

        $staticFiles = collect([
            'manifest.json',
            'Component-preload.js',
            'Component-preload.js.map',
            'Component-dbg.js',
            'Component-dbg.js.map',
            'i18n/i18n.properties',
        ]);

        $assets = $staticFiles->merge($i18nFiles)->unique()->values()->all();

        $this->copyDistAssets($sourcePath . 'dist', $targetPath, $assets);

        // Register source
        $this->writeSourceOverride("$phpNamespace\\$moduleClassName", $sourcePath);

        $operation = $exists ? 'Updated' : 'Created';
        $this->components->success("{$operation} Ui5App module `{$appName}`");

        return self::SUCCESS;
    }
}
