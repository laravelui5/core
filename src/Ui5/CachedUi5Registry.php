<?php

namespace LaravelUi5\Core\Ui5;

use Illuminate\Contracts\Container\BindingResolutionException;
use LaravelUi5\Core\Enums\ArtifactType;
use LaravelUi5\Core\Ui5\Contracts\SluggableInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ActionInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5RegistryInterface;

class CachedUi5Registry implements Ui5RegistryInterface
{
    protected array $modules = [];
    protected array $artifacts = [];
    protected array $namespaceToModule = [];
    protected array $artifactToModule = [];
    protected array $slugs = [];

    public function __construct()
    {
        $cachePath = base_path('bootstrap/cache/ui5.php');

        if (file_exists($cachePath)) {
            $data = require $cachePath;
            $this->modules = $data['modules'] ?? [];
            $this->artifacts = $data['artifacts'] ?? [];
            $this->namespaceToModule = $data['namespaceToModule'] ?? [];
            $this->artifactToModule = $data['artifactToModule'] ?? [];
            $this->slugs = $data['slugs'] ?? [];
        }
    }

    public function hasModule(string $slug): bool
    {
        return isset($this->modules[$slug]);
    }

    /**
     * @throws BindingResolutionException
     */
    public function getModule(string $slug): ?Ui5ModuleInterface
    {
        $entry = $this->modules[$slug] ?? null;

        if ($entry instanceof Ui5ModuleInterface) {
            return $entry;
        }

        if (is_string($entry) && class_exists($entry)) {
            $instance = app()->make($entry, ['slug' => $slug]);
            return $this->modules[$slug] = $instance;
        }

        return null;
    }

    public function artifactToModuleSlug(string $class): ?string
    {
        return $this->artifactToModule[$class] ?? null;
    }

    public function namespaceToModuleSlug(string $namespace): ?string
    {
        return $this->namespaceToModule[$namespace] ?? null;
    }

    public function modules(): array
    {
        return $this->modules;
    }

    public function has(string $namespace): bool
    {
        return isset($this->artifacts[$namespace]);
    }

    /**
     * @throws BindingResolutionException
     */
    public function get(string $namespace): ?Ui5ArtifactInterface
    {
        $entry = $this->artifacts[$namespace] ?? null;

        if ($entry instanceof Ui5ArtifactInterface) {
            return $entry;
        }

        if (is_string($entry) && isset($this->artifactToModule[$entry])) {
            $slug = $this->artifactToModule[$entry];
            $instance = app()->make($entry, ['slug' => $slug]);
            return $this->artifacts[$namespace] = $instance;
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
