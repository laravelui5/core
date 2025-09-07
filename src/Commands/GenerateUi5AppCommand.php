<?php

namespace LaravelUi5\Core\Commands;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Exception;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;
use Throwable;

class GenerateUi5AppCommand extends BaseGenerator
{
    protected $signature = 'ui5:app {name : The name of the ui5 app}
        {--package-prefix=pragmatiqu : The composer package namespace prefix}
        {--php-ns-prefix=Pragmatiqu : The namespace prefix for the php package}
        {--js-ns-prefix=io.pragmatiqu : The JS namespace prefix}
        {--create : Create a new module from scratch}
        {--refresh : Overwrite existing files without confirmation}
        {--vendor="Pragmatiqu IT GmbH" : The vendor of the module}';

    protected $description = 'Generate a Ui5App implementation from a UI5 frontend project';

    /**
     * @var string $appName the name of the generated class extending Ui5AppInterface.
     */
    protected string $appName;

    /**
     * @var string $ui5AppFolderName the name of the folder containing the Ui5 JS/TS app.
     */
    protected string $ui5AppFolderName;

    /**
     * @var string $sourcePath the folder containing the Ui5 JS/TS App, per convention `../ui5-{$this->ui5AppFolderName}/`.
     */
    protected string $sourcePath;

    /**
     * @var string $targetPath the folder containing the generated Ui5App class, per convention `ui5/{$this->appName}/src/`.
     */
    protected string $targetPath;

    /**
     * @var string $className the name of the PHP class, per convention `{$this->appName}App`.
     */
    protected string $className;

    protected string $moduleClassName;

    /**
     * @var string $phpNamespace the namespace of the PHP class, per convention `Pragmatiqu\LaravelUi5\{$this->appName}`.
     */
    protected string $phpNamespace;

    /**
     * @var string $targetFile the full path of the target file, per convention `{$this->targetPath}{$this->className}.php`.
     */
    protected string $targetFile;

    protected string $targetModuleFile;

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle(): int
    {
        try {
            $this->initConventions();
            $this->checkSourceFiles();
            $params = $this->extractParameters()->toArray();
            $this->checkParams($params);
            if ($this->isCreate())
            {
                $this->create($params);
                $this->components->success("Generated Ui5App module `{$this->appName}`");
            }
            else {
                $this->update($params);
                $this->components->success("Updated Ui5App module `{$this->appName}`");
            }
            return self::SUCCESS;
        } catch (Exception $e) {
            $lines = explode("\n", $e->getMessage());
            if (!empty($lines)) {
                $this->components->error($lines[0]);

                foreach (array_slice($lines, 1) as $line) {
                    $this->components->info($line);
                }
            }

            return self::FAILURE;
        }
    }

    /**
     * @throws Exception
     */
    protected function initConventions(): void
    {
        $this->appName = $this->argument('name');
        $this->assertCamelCase('Ui5App', $this->appName);

        $phpNamespacePrefix = rtrim($this->option('php-ns-prefix'), '\\');
        $jsNamespacePrefix = rtrim($this->option('js-ns-prefix'), '.');

        $this->ui5AppFolderName = Str::kebab($this->appName);

        $conventionPaths = [
            base_path("../ui5-{$this->ui5AppFolderName}/"),
            base_path("../{$jsNamespacePrefix}.{$this->ui5AppFolderName}/"),
        ];
        try {
            $this->sourcePath = collect($conventionPaths)->first(fn($path) => File::exists($path));
        } catch (Throwable $e) {
            throw new Exception("Source folder for UI5 app not found. Tried:\n - " . implode("\n - ", $conventionPaths));
        }

        $this->targetPath = base_path("ui5/{$this->appName}/src/");
        $this->className = "{$this->appName}App";
        $this->moduleClassName = "{$this->appName}Module";
        $this->phpNamespace = "{$phpNamespacePrefix}\\{$this->appName}";
        $this->targetFile = "{$this->targetPath}{$this->className}.php";
        $this->targetModuleFile = "{$this->targetPath}{$this->moduleClassName}.php";

        if ($this->output->isVerbose()) {
            $this->info("sourcePath: {$this->sourcePath}");
            $this->info("targetPath: {$this->targetPath}");
            $this->info("className: {$this->className}");
            $this->info("moduleClassName: {$this->moduleClassName}");
            $this->info("phpNamespace: {$this->phpNamespace}");
            $this->info("targetFile: {$this->targetFile}");
            $this->info("targetModuleFile: {$this->targetModuleFile}");
        }
    }

    /**
     * @throws Exception
     */
    protected function checkSourceFiles(): void
    {
        $requiredFiles = [
            'ui5.yaml' => $this->sourcePath . 'ui5.yaml',
            'package.json' => $this->sourcePath . 'package.json',
            'dist/index.html' => $this->sourcePath . 'dist/index.html',
            'dist/manifest.json' => $this->sourcePath . 'dist/manifest.json',
            'dist/i18n/i18n.properties' => $this->sourcePath . 'dist/i18n/i18n.properties',
        ];

        $missing = [];

        foreach ($requiredFiles as $label => $path)
            if (!File::exists($path)) {
                $missing[$label] = $path;
            } elseif ($this->output->isVerbose()) {
                $this->info("Found: {$label}");
            }

        if (!empty($missing)) {
            $report = collect($missing)
                ->map(fn($path, $label) => "Missing: {$label}\n expected at: {$path}")
                ->implode("\n");

            throw new Exception("Source check failed:\n\n{$report}\n\nHint: Make sure to run `npm run build` inside your UI5 app.");
        }
    }

    protected function extractParameters(): Collection
    {
        return collect()
            ->merge($this->extractFromYaml())
            ->merge($this->extractFromPackageJson())
            ->merge($this->extractFromIndexHtml())
            ->merge($this->extractFromManifest())
            ->merge($this->extractFromI18n());
    }

    protected function extractFromYaml(): array
    {
        $yaml = Yaml::parseFile($this->sourcePath . 'ui5.yaml');

        return [
            'ui5Namespace' => $yaml['metadata']['name'] ?? null,
            'ui5Version' => $yaml['framework']['version'] ?? '1.0.0',
        ];
    }

    protected function extractFromPackageJson(): array
    {
        $path = $this->sourcePath . 'package.json';
        $json = json_decode(file_get_contents($path), true);

        return [
            'appVersion' => $json['version'] ?? null,
        ];
    }

    protected function extractFromIndexHtml(): array
    {
        $html = file_get_contents($this->sourcePath . 'dist/index.html');
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $script = $xpath->query('//script[@id="sap-ui-bootstrap"]')->item(0);

        $bootstrap = [];
        $namespaces = [];

        if ($script instanceof DOMElement) {
            foreach ($script->attributes as $attr) {
                if (str_starts_with($attr->name, 'data-sap-ui-')) {
                    $key = str_replace('data-sap-ui-', '', $attr->name);

                    if ($key === 'resource-roots') {
                        $roots = json_decode($attr->value, true);
                        $namespaces = array_keys($roots);
                    } else {
                        $bootstrap[$key] = $attr->value;
                    }
                }
            }
        }

        $inlineScript = $xpath->query('//script[not(@src)]')->item(0)?->nodeValue ?? '';
        $inlineCss = $xpath->query('//style')->item(0)?->nodeValue ?? '';

        return [
            'bootstrapAttributes' => $bootstrap,
            'resourceNamespaces' => $namespaces,
            'inlineScript' => trim($inlineScript),
            'inlineCss' => trim($inlineCss),
        ];
    }

    protected function extractFromManifest(): array
    {
        $json = json_decode(file_get_contents($this->sourcePath . 'dist/manifest.json'), true);
        $sapUi5 = $json['sap.ui5'] ?? [];
        return [
            'sap.ui5' => json_encode($sapUi5, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT),
        ];
    }

    protected function extractFromI18n(): array
    {
        $path = $this->sourcePath . 'dist/i18n/i18n.properties';
        $title = null;
        $description = null;

        foreach (file($path) as $line)
            if (str_starts_with($line, 'appTitle=')) {
                $title = trim(substr($line, 9)) ?? 'Empty title';
            } elseif (str_starts_with($line, 'appDescription=')) {
                $description = trim(substr($line, 15)) ?? 'Empty description';
            }

        return [
            'title' => $title,
            'description' => $description,
        ];
    }

    /**
     * @throws Exception
     */
    protected function checkParams(array $params): void
    {
        $module = Str::kebab($this->appName);
        $appId = $params['ui5Namespace'];
        $lastDot = strrpos($appId, '.');
        $expectedId = $lastDot === false
            ? $module
            : substr($appId, 0, $lastDot) . '.' . $module;
        if (!is_string($appId) || $appId !== $expectedId) {
            throw new Exception("Mismatch in sap.app/id: expected '{$expectedId}', but found '{$appId}'.");
        }
    }

    /**
     * @throws Exception
     */
    protected function isCreate(): bool
    {
        $create = $this->option('create');
        $refresh = $this->option('refresh');
        $moduleExists = File::exists($this->targetFile);

        if ($create && $moduleExists) {
            throw new Exception("Module already exists. Use --refresh to update.");
        }

        if ($refresh && !$moduleExists) {
            throw new Exception("Module does not exist. Use --create to scaffold.");
        }

        if (!$create && !$refresh) {
            if ($moduleExists) {
                throw new Exception("Module already exists. Use --refresh to update.");
            } else {
                throw new Exception("Module does not exist. Use --create to scaffold.");
            }
        }

        return $create;
    }

    protected function create(array $params): void
    {
        File::ensureDirectoryExists($this->targetPath);

        // composer.json
        $this->files->put("{$this->targetPath}../composer.json", $this->compileStub('composer.stub', [
            'packagePrefix' => $this->option('package-prefix'),
            'urlKey' => $this->ui5AppFolderName,
            'description' => $params['description'],
            'namespace' => json_encode($this->phpNamespace),
        ]));

        // ServiceProvider
        $this->files->put("{$this->targetPath}/{$this->appName}ServiceProvider.php", $this->compileStub('ServiceProvider.stub', [
            'namespace' => $this->phpNamespace,
            'name' => $this->appName,
        ]));

        // Module
        $this->files->put($this->targetFile, $this->compileStub('Ui5ModuleApp.stub', [
            'phpNamespace' => $this->phpNamespace,
            'class' => $this->className,
            'moduleClass' => $this->moduleClassName,
        ]));

        // Manifest
        $this->files->put("{$this->targetPath}/{$this->appName}Manifest.php", $this->compileStub('Ui5Manifest.stub', [
            'phpNamespace' => $this->phpNamespace,
            'class' => $this->appName
        ]));

        $this->update($params);
    }

    protected function update(array $params): void
    {
        // app
        $this->files->put($this->targetFile, $this->compileStub('Ui5App.stub', [
            'name' => $this->appName,
            'namespace' => $this->phpNamespace,
            'class' => $this->className,
            'ui5Namespace' => $params['ui5Namespace'],
            'appVersion' => $params['appVersion'],
            'title' => addslashes($params['title']),
            'description' => addslashes($params['description']),
            'bootstrapAttributes' => var_export($params['bootstrapAttributes'], true),
            'resourceNamespaces' => var_export($params['resourceNamespaces'], true),
            'sap.ui5' => $params['sap.ui5'],
            'inlineScript' => $params['inlineScript'],
            'inlineCss' => $params['inlineCss'],
            'vendor' => $this->option('vendor'),
        ]));

        // dist assets
        $i18nFiles = collect(File::files($this->sourcePath . 'dist/i18n'))
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

        $this->copyDistAssets($this->sourcePath . 'dist', $this->targetPath, $assets);
    }
}
