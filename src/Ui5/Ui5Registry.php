<?php

namespace LaravelUi5\Core\Ui5;

use LaravelUi5\Core\Enums\ArtifactType;
use LaravelUi5\Core\Ui5\Contracts\SluggableInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ActionInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5RegistryInterface;

class Ui5Registry implements Ui5RegistryInterface
{
    protected array $modules = [];
    protected array $artifacts = [];
    protected array $namespaceToModule = [];
    protected array $slugs = [];

    public function __construct()
    {
        $config = config('ui5');

        $modules = $config['modules'] ?? [];
        foreach ($modules as $slug => $moduleClass) {

            /** @var Ui5ModuleInterface $module */
            $module = new $moduleClass($slug);

            $this->modules[$slug] = $module;

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
        }

        $dashboards = $config['dashboards'] ?? [];
        foreach ($dashboards as $dashboardClass) {
            $dashboard = new $dashboardClass;
            $this->registerArtifact($dashboard, null);
        }

        $reports = $config['reports'] ?? [];
        foreach ($reports as $reportClass) {
            $report = new $reportClass;
            $this->registerArtifact($report, null);
        }
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
        }

        if ($artifact instanceof SluggableInterface) {
            $urlKey = ArtifactType::urlKeyFromArtifact($artifact, $moduleSlug);
            $this->slugs[$urlKey] = $artifact;
        }
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

    public function namespaceToModuleSlug(string $namespace): ?string
    {
        return $this->namespaceToModule[$namespace] ?? null;
    }

    public function artifactToModuleSlug(string $class): ?string
    {
        return null;
    }

    public function modules(): array
    {
        return $this->modules;
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

    public function all(): array
    {
        return $this->artifacts;
    }

    /** -- Laravel routing ------------------------------------------------- */

    public function fromSlug(string $slug): ?Ui5ArtifactInterface
    {
        return $this->slugs[$slug] ?? null;
    }

    public function slugFor(Ui5ArtifactInterface $artifact): ?string
    {
        $namespace = $artifact->getNamespace();

        if (!isset($this->namespaceToModule[$namespace])) {
            throw new \RuntimeException("No module mapping found for artifact: {$namespace}");
        }

        $module = $this->namespaceToModule[$namespace];

        return $artifact instanceof SluggableInterface
            ? ArtifactType::urlKeyFromArtifact($artifact, $module)
            : null;
    }

    /** -- Client facing --------------------------------------------------- */

    public function resolve(string $namespace): ?string
    {
        $artifact = $this->get($namespace);
        if ($artifact) {
            return '/ui5/' . $this->slugFor($artifact) . '/' . $artifact->getVersion();
        }

        return null;
    }

    public function resolveRoots(array $namespaces): array
    {
        return collect($namespaces)->mapWithKeys(fn($ns) => [$ns => $this->resolve($ns)])->all();
    }
}
