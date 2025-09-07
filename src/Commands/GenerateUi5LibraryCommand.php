<?php

namespace LaravelUi5\Core\Commands;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;
use Throwable;

class GenerateUi5LibraryCommand extends BaseGenerator
{
    protected $signature = 'ui5:lib
                            {name : The CamelCase name of the library}
                            {--php-ns-prefix=Pragmatiqu : The PHP namespace prefix}
                            {--js-ns-prefix=io.pragmatiqu : The JS namespace prefix}
                            {--create : Create a new module from scratch}
                            {--refresh : Refresh metadata and assets from source project}';

    protected $description = 'Generate a Ui5Library implementation from a UI5 library build';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

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

        $sourcePath = collect($conventionPaths)->first(fn($path) => File::exists($path));
        if (is_null($sourcePath)) {
            $this->components->error("Source folder for UI5 lib not found. Tried:");
            foreach ($conventionPaths as $conventionPath) {
                $this->components->info("- {$conventionPath}");
            }
            return self::FAILURE;
        }

        $targetPath = base_path("ui5/{$name}/src/");
        $classPath = base_path("ui5/{$name}/src/{$name}Library.php");
        $modulePath = base_path("ui5/{$name}/src/{$name}Module.php");

        $create = $this->option('create');
        $refresh = $this->option('refresh');
        $moduleExists = File::exists($classPath);

        // Decision tree
        if ($create && $moduleExists) {
            $this->components->error("Module already exists. Use --refresh to update.");
            return 1;
        }

        if ($refresh && !$moduleExists) {
            $this->components->error("Module does not exist. Use --create to scaffold.");
            return 1;
        }

        if (!$create && !$refresh) {
            if ($moduleExists) {
                $this->components->info("Module already exists. Use --refresh to update.");
            } else {
                $this->components->info("Module does not exist. Use --create to scaffold.");
            }
            return 0;
        }

        $yaml = Yaml::parseFile($sourcePath . 'ui5.yaml');
        $ui5Namespace = $yaml['metadata']['name'] ?? null;
        if (!$ui5Namespace || !is_string($ui5Namespace)) {
            $this->components->error("Invalid or missing namespace in ui5.yaml");
            return 1;
        }

        $distPath = $sourcePath . 'dist/resources/' . Str::of($ui5Namespace)->replace('.', '/');
        $libraryPath = "{$distPath}/.library";
        if (!File::exists($libraryPath)) {
            $this->components->error("Failed to load .library file at: {$libraryPath}");
            $this->newLine();
            $this->components->info("Run `npm run build` to create library assets.");
            return 1;
        }

        $libraryXml = simplexml_load_file($libraryPath);
        $title = (string)$libraryXml->title ?? 'Untitled';
        $description = (string)$libraryXml->documentation ?? 'No description';
        $vendor = (string)$libraryXml->vendor ?? 'Vendor not supplied';

        $package = json_decode(file_get_contents($sourcePath . 'package.json'), true);
        $version = $package['version'] ?? '1.0.0';

        File::ensureDirectoryExists($targetPath);

        // Generate class
        $this->files->put($classPath, $this->compileStub('Ui5Library.stub', [
            'namespace' => $phpNamespace,
            'class' => "{$name}Library",
            'ui5Namespace' => $ui5Namespace,
            'version' => $version,
            'title' => $title,
            'description' => $description,
            'vendor' => $vendor,
        ]));

        $this->files->put($modulePath, $this->compileStub('Ui5ModuleLib.stub', [
            'phpNamespace' => $phpNamespace,
            'class' => "{$name}Library",
            'moduleClass' => "{$name}Module",
        ]));

        // Copy artefacts
        $i18nFiles = collect(File::files($sourcePath))
            ->filter(fn($f) => Str::endsWith($f->getFilename(), '.properties'))
            ->map(fn($f) => $f->getFilename())
            ->all();

        $staticFiles = collect([
            'library-preload.js',
            'library-preload.js.map',
            'library-dbg.js',
            'library-dbg.js.map',
        ]);

        $assets = $staticFiles->merge($i18nFiles)->unique()->values()->all();

        $this->copyDistAssets($distPath, $targetPath, $assets);

        $operation = $moduleExists ? 'Updated' : 'Created';
        $this->components->success("{$operation} Ui5Library module `{$name}`");

        return self::SUCCESS;
    }
}
