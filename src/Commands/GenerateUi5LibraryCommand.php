<?php

namespace LaravelUi5\Core\Commands;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use JsonException;
use LaravelUi5\Core\Commands\Concerns\RunsUi5Build;
use LaravelUi5\Core\Commands\Concerns\WritesSourceOverride;
use LaravelUi5\Core\Ui5\Contracts\Ui5LibrarySource;
use LogicException;

class GenerateUi5LibraryCommand extends BaseGenerator
{
    use RunsUi5Build;
    use WritesSourceOverride;

    protected $signature = 'ui5:lib
                            {name : The CamelCase name of the library}
                            {--php-ns-prefix=Pragmatiqu : The PHP namespace prefix}
                            {--js-ns-prefix=io.pragmatiqu : The JS namespace prefix}
                            {--create : Create a new module from scratch}
                            {--refresh : Refresh metadata and assets from source project}
                            {--auto-build : Run UI5 build before importing assets}';

    protected $description = 'Generate a Ui5Library module from a UI5 library project';

    public function __construct(
        protected Filesystem $files
    )
    {
        parent::__construct();
    }

    /**
     * @throws JsonException
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $this->assertCamelCase('Ui5Library', $name);

        $kebab = Str::kebab($name);
        $phpPrefix = rtrim($this->option('php-ns-prefix'), '\\');
        $phpNamespace = "{$phpPrefix}\\{$name}";
        $jsPrefix = rtrim($this->option('js-ns-prefix'), '.');

        // Support both LaravelUi5 and Easy UI5 conventions
        $conventionPaths = [
            base_path("../ui5-{$kebab}-lib/"),
            base_path("../{$jsPrefix}.{$kebab}/"),
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

        if ($this->option('auto-build')) {
            $this->runBuild($sourcePath);
        }

        // Build source model
        $source = Ui5LibrarySource::fromWorkspace($sourcePath);

        $targetPath = base_path("ui5/{$name}/src/");
        $classPath = base_path("ui5/{$name}/src/{$name}Library.php");
        $modulePath = base_path("ui5/{$name}/src/{$name}Module.php");

        $create = $this->option('create');
        $refresh = $this->option('refresh');
        $exists = File::exists($classPath);

        // Decision tree
        if ($create && $exists) {
            $this->components->error("Module already exists. Use --refresh to update.");
            return 1;
        }

        if ($refresh && !$exists) {
            $this->components->error("Module does not exist. Use --create to scaffold.");
            return 1;
        }

        if (!$create && !$refresh) {
            if ($exists) {
                $this->components->info("Module already exists. Use --refresh to update.");
            } else {
                $this->components->info("Module does not exist. Use --create to scaffold.");
            }
            return 0;
        }

        File::ensureDirectoryExists($targetPath);


        // Generate Ui5Library class
        $descriptor = $source->getDescriptor();
        $this->files->put($classPath, $this->compileStub('Ui5Library.stub', [
            'namespace' => $phpNamespace,
            'class' => "{$name}Library",
            'ui5Namespace' => $descriptor->getNamespace(),
            'version' => $descriptor->getVersion(),
            'title' => $descriptor->getTitle(),
            'description' => $descriptor->getDescription(),
            'vendor' => $descriptor->getVendor(),
        ]));

        // Generate Ui5Module class
        $this->files->put($modulePath, $this->compileStub('Ui5ModuleLib.stub', [
            'phpNamespace' => $phpNamespace,
            'class' => "{$name}Library",
            'moduleClass' => "{$name}Module",
            'name' => json_encode($name),
        ]));

        // Copy artefacts
        $this->copyLibraryAssets($source, $targetPath);

        // Register source
        $this->writeSourceOverride("$phpNamespace\\{$name}Module", $sourcePath);

        $operation = $exists ? 'Updated' : 'Created';
        $this->components->success("{$operation} Ui5Library module `{$name}`");

        return self::SUCCESS;
    }

    protected function copyLibraryAssets(Ui5LibrarySource $source, string $target): void
    {
        $distPath = $source->getSourcePath()
            . '/dist/resources/'
            . str_replace('.', '/', $source->getDescriptor()->getNamespace());

        $staticFiles = [
            'manifest.json',
            'library-preload.js',
            'library-preload.js.map',
            'library-dbg.js',
            'library-dbg.js.map',
        ];

        $i18nFiles = collect(File::files($distPath))
            ->filter(fn($f) => Str::endsWith($f->getFilename(), '.properties'))
            ->map(fn($f) => $f->getFilename())
            ->all();

        $assets = array_unique(array_merge($staticFiles, $i18nFiles));

        $this->copyDistAssets($distPath, $target, $assets);
    }
}
