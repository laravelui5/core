<?php

namespace LaravelUi5\Core\Ui5;

use LaravelUi5\Core\Attributes\Setting;
use LaravelUi5\Core\Enums\ArtifactType;
use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5RegistryInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5SourceStrategyInterface;
use LaravelUi5\Core\Ui5CoreServiceProvider;
use LogicException;
use ReflectionClass;
use ReflectionException;

class Ui5Registry implements Ui5RegistryInterface
{
    /**
     * @var array<string, Ui5ModuleInterface>
     */
    protected array $modules = [];

    /**
     * @var array<string, Ui5ArtifactInterface>
     */
    protected array $artifacts = [];

    /**
     * @var array<string, string>
     */
    protected array $namespaceToModule = [];

    /**
     * @var array<class-string<Ui5ArtifactInterface>, string>
     */
    protected array $artifactToModule = [];

    /**
     * @var array<string, Ui5ArtifactInterface>
     */
    protected array $slugs = [];

    /**
     * @var array<string, array<string, string[]>>
     */
    protected array $settings = [];

    /**
     * @var array<class-string<Ui5ModuleInterface>,string>
     */
    private array $sourceOverrides = [];

    /**
     * @throws ReflectionException
     */
    public function __construct(?array $config = null)
    {
        if ($config) {
            $this->loadFromArray($config);
        } else {
            $this->loadFromArray(config('ui5'));
        }
    }

    /**
     * @throws ReflectionException
     */
    public static function fromArray(array $config): self
    {
        return new self($config);
    }

    protected function loadSourceOverrides(): void
    {
        $path = base_path('.ui5-sources.php');

        if (!is_file($path)) {
            return;
        }

        $config = require $path;

        $modules = $config['modules'] ?? [];

        $overrides = [];

        foreach ($modules as $moduleClass => $relativePath) {
            if (!is_string($moduleClass) || !is_string($relativePath)) {
                continue;
            }

            $absolutePath = base_path($relativePath);

            if (is_dir($absolutePath)) {
                $overrides[$moduleClass] = $absolutePath;
            }
        }

        $this->sourceOverrides = $overrides;
    }

    /**
     * @throws ReflectionException
     */
    protected function loadFromArray(array $config): void
    {
        $modules = $config['modules'] ?? [];

        $this->loadSourceOverrides();

        // Pass 1: Instantiate modules
        foreach ($modules as $slug => $moduleClass) {

            if (!class_exists($moduleClass)) {
                throw new LogicException("UI5 module class [{$moduleClass}] does not exist.");
            }

            $strategy = $this->resolveSourceStrategy($moduleClass);

            /** @var Ui5ModuleInterface $module */
            $module = new $moduleClass($slug, $strategy);

            $this->modules[$slug] = $module;
        }

        // Pass 2: Reflect everything else
        $dashboards = $config['dashboards'] ?? [];
        $reports = $config['reports'] ?? [];
        $dialogs = $config['dialogs'] ?? [];

        foreach ($this->modules as $slug => $module) {

            if ($module->hasApp() && ($app = $module->getApp())) {
                $this->registerArtifact($app, $slug);
            }
            if ($module->hasLibrary() && ($lib = $module->getLibrary())) {
                $this->registerArtifact($lib, $slug);
            }
            foreach ($module->getCards() as $card) {
                $this->registerArtifact($card, $slug);
            }
            foreach ($module->getKpis() as $kpi) {
                $this->registerArtifact($kpi, $slug);
            }
            foreach ($module->getTiles() as $tile) {
                $this->registerArtifact($tile, $slug);
            }
            foreach ($module->getActions() as $action) {
                $this->registerArtifact($action, $slug);
            }
            foreach ($module->getResources() as $resource) {
                $this->registerArtifact($resource, $slug);
            }
            foreach ($module->getReports() as $report) {
                $key = get_class($report);
                if (array_key_exists($key, $reports)) {
                    $report->setSlug($reports[$key]);
                    $this->registerArtifact($report, $slug);
                }
            }
            foreach ($module->getDashboards() as $dashboard) {
                $key = get_class($dashboard);
                if (array_key_exists($key, $dashboards)) {
                    $dashboard->setSlug($dashboards[$key]);
                    $this->registerArtifact($dashboard, $slug);
                }
            }
            foreach ($module->getDialogs() as $dialog) {
                $key = get_class($dialog);
                if (array_key_exists($key, $dialogs)) {
                    $dialog->setSlug($dialogs[$key]);
                    $this->registerArtifact($dialog, $slug);
                }
            }
        }

        // Extension Hook
        $this->afterLoad($config);
    }

    protected function afterLoad(array $config): void
    {
        // extension hook (no-op by default)
    }

    /**
     * @throws ReflectionException
     */
    public function resolveSourceStrategy(string $moduleClass): Ui5SourceStrategyInterface
    {
        if (isset($this->sourceOverrides[$moduleClass])) {
            return new WorkspaceStrategy($this->sourceOverrides[$moduleClass]);
        }

        $ref = new ReflectionClass($moduleClass);

        $moduleDir = dirname($ref->getFileName());

        // Convention:
        // ui5/<Module>/src/ → ui5/<Module>/resources/ui5
        $packagePath = realpath($moduleDir . '/../resources/ui5');

        if ($packagePath && is_dir($packagePath)) {
            return new PackageStrategy($packagePath);
        }

        throw new LogicException(
            "Unable to resolve UI5 source path for module [{$moduleClass}] from {$moduleDir}."
        );
    }

    /**
     * Registers a UI5 artifact within the registry.
     *
     * This method adds the artifact to the internal lookup maps by namespace
     * and module context. If the artifact is sluggable (i.e., addressable via URI),
     * it also registers the composed `urlKey` for reverse lookup.
     *
     * @param Ui5ArtifactInterface $artifact
     * @param string|null $moduleSlug
     */
    protected function registerArtifact(Ui5ArtifactInterface $artifact, ?string $moduleSlug): void
    {
        $namespace = $artifact->getNamespace();
        $this->artifacts[$namespace] = $artifact;

        if (null !== $moduleSlug) {
            $this->namespaceToModule[$namespace] = $moduleSlug;
            $this->artifactToModule[get_class($artifact)] = $moduleSlug;
        }

        $this->discoverSettings($artifact);

        $urlKey = ArtifactType::urlKeyFromArtifact($artifact);
        if (null !== $urlKey) {
            $this->slugs[$urlKey] = $artifact;
        }
    }

    /**
     * Discover and register Setting attributes defined on Ui5 artifacts.
     *
     * @param Ui5ArtifactInterface $artifact
     */
    protected function discoverSettings(Ui5ArtifactInterface $artifact): void
    {
        $ref = new ReflectionClass($artifact);
        $attributes = $ref->getAttributes(Setting::class);

        if (empty($attributes)) {
            return;
        }

        $namespace = $artifact->getModule()->getArtifactRoot()->getNamespace();

        foreach ($attributes as $attr) {
            /** @var Setting $setting */
            $setting = $attr->newInstance();

            if (array_key_exists($setting->setting, $this->settings[$namespace] ?? [])) {
                throw new LogicException(sprintf(
                    'Duplicate setting [%s] found in [%s].',
                    $setting->setting,
                    get_class($artifact)
                ));
            }

            $this->settings[$namespace][$setting->setting] = [
                'default' => $setting->default,
                'type' => $setting->type,
                'scope' => $setting->scope,
                'role' => $setting->role,
                'note' => $setting->note,
            ];
        }
    }

    /** -- Lookup ---------------------------------------------------------- */

    public function modules(): array
    {
        return $this->modules;
    }

    public function hasModule(string $slug): bool
    {
        return isset($this->modules[$slug]);
    }

    public function getModule(string $slug): ?Ui5ModuleInterface
    {
        if (isset($this->modules[$slug])) {
            return $this->modules[$slug];
        }

        return null;
    }

    public function artifacts(): array
    {
        return $this->artifacts;
    }

    public function has(string $namespace): bool
    {
        return isset($this->artifacts[$namespace]);
    }

    public function get(string $namespace): ?Ui5ArtifactInterface
    {
        if (isset($this->artifacts[$namespace])) {
            return $this->artifacts[$namespace];
        }

        return null;
    }

    public function settings(?string $namespace = null): array
    {
        if (null === $namespace) {
            return $this->settings;
        }

        return $this->settings[$namespace] ?? [];
    }

    /** -- Laravel routing ------------------------------------------------- */

    public function fromSlug(string $slug): ?Ui5ArtifactInterface
    {
        return $this->slugs[$slug] ?? null;
    }

    public function resolve(string $namespace): ?string
    {
        $artifact = $this->get($namespace);
        if ($artifact) {
            $slug = ArtifactType::urlKeyFromArtifact($artifact);

            $prefix = Ui5CoreServiceProvider::UI5_ROUTE_PREFIX;

            return "/{$prefix}/{$slug}/{$artifact->getVersion()}";
        }

        return null;
    }

    public function resolveRoots(array $namespaces): array
    {
        return collect($namespaces)->mapWithKeys(fn($ns) => [$ns => $this->resolve($ns)])->all();
    }

    /** -- Export ---------------------------------------------------------- */
    public function exportToCache(): array
    {
        return [
            'modules' => $this->modules,
            'artifacts' => $this->artifacts,
            'namespaceToModule' => $this->namespaceToModule,
            'artifactToModule' => $this->artifactToModule,
            'slugs' => $this->slugs,
            'settings' => $this->settings,
            'sourceOverrides' => $this->sourceOverrides,
        ];
    }
}
