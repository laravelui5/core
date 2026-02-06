<?php

namespace LaravelUi5\Core\Ui5;

use LaravelUi5\Core\Attributes\Setting;
use LaravelUi5\Core\Infrastructure\Contracts\Ui5SourceStrategyResolverInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5RegistryInterface;
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
     * @var array<string, array<string, string[]>>
     */
    protected array $settings = [];

    protected Ui5SourceStrategyResolverInterface $sourceStrategyResolver;

    protected Ui5InfrastructureCollector $collector;

    /**
     * @throws ReflectionException
     */
    public function __construct(
        Ui5SourceStrategyResolverInterface $source,
        Ui5InfrastructureCollector         $infra,
        ?array                             $config = null
    )
    {
        $this->sourceStrategyResolver = $source;
        $this->collector = $infra;

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
        return new self(
            app(Ui5SourceStrategyResolverInterface::class),
            app(Ui5InfrastructureCollector::class),
            $config
        );
    }

    /**
     * @throws ReflectionException
     */
    protected function loadFromArray(array $config): void
    {
        $modules = array_merge($config['modules'] ?? [], $this->collector->all());

        // Pass 1: Instantiate modules
        foreach ($modules as $class) {

            if (!class_exists($class)) {
                throw new LogicException("UI5 module class `{$class}` does not exist.");
            }

            $strategy = $this->sourceStrategyResolver->resolve($class);

            /** @var Ui5ModuleInterface $module */
            $module = new $class($strategy);

            $this->modules[$module->getName()] = $module;
        }

        // Pass 2: Reflect everything else
        foreach ($this->modules as $module) {
            foreach ($module->getAllArtifacts() as $artifact) {
                $this->registerArtifact($artifact);
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
     * Registers a UI5 artifact within the registry.
     *
     * This method adds the artifact to the internal lookup maps by namespace
     * and module context. If the artifact is sluggable (i.e., addressable via URI),
     * it also registers the composed `urlKey` for reverse lookup.
     *
     * @param Ui5ArtifactInterface $artifact
     */
    protected function registerArtifact(Ui5ArtifactInterface $artifact): void
    {
        $namespace = $artifact->getNamespace();
        $moduleNamespace = $artifact->getModule()->getName();
        $this->artifacts[$namespace] = $artifact;
        $this->namespaceToModule[$namespace] = $moduleNamespace;
        $this->artifactToModule[get_class($artifact)] = $moduleNamespace;

        $this->discoverSettings($artifact);
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

            if (array_key_exists($setting->key, $this->settings[$namespace] ?? [])) {
                throw new LogicException(sprintf(
                    'Duplicate setting [%s] found in [%s].',
                    $setting->key,
                    get_class($artifact)
                ));
            }

            $this->settings[$namespace][$setting->key] = [
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

    public function getModule(string $namespace): ?Ui5ModuleInterface
    {
        return $this->modules[$namespace] ?? null;
    }

    public function artifacts(): array
    {
        return $this->artifacts;
    }

    public function get(string $namespace): ?Ui5ArtifactInterface
    {
        return $this->artifacts[$namespace] ?? null;
    }

    public function settings(?string $namespace = null): array
    {
        if (null === $namespace) {
            return $this->settings;
        }

        return $this->settings[$namespace] ?? [];
    }

    /** -- Laravel routing ------------------------------------------------- */

    public function pathToNamespace(string $namespace): string
    {
        return str_replace('/', '.', trim($namespace, '/'));
    }

    public function namespaceToPath(string $namespace): string
    {
        return str_replace('.', '/', trim($namespace, '.'));
    }

    public function resolve(string $namespace): ?string
    {
        $artifact = $this->get($namespace);
        if ($artifact) {
            $prefix = Ui5CoreServiceProvider::UI5_ROUTE_PREFIX;
            $typePrefix = $artifact->getType()->routePrefix();
            $path = $this->namespaceToPath($namespace);
            $version = $artifact->getVersion();

            return "/{$prefix}/{$typePrefix}/{$path}@{$version}";
        }

        return null;
    }

    public function resolveRoots(array $namespaces): array
    {
        $roots = [];
        foreach ($namespaces as $ns) {
            $roots[$ns] = $this->resolve($ns);
        }
        return $roots;
    }

    /** -- Export ---------------------------------------------------------- */
    public function exportToCache(): array
    {
        return [
            'modules' => $this->modules,
            'artifacts' => $this->artifacts,
            'namespaceToModule' => $this->namespaceToModule,
            'artifactToModule' => $this->artifactToModule,
            'settings' => $this->settings,
        ];
    }
}
