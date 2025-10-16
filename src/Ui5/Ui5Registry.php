<?php

namespace LaravelUi5\Core\Ui5;

use LaravelUi5\Core\Attributes\Ability;
use LaravelUi5\Core\Attributes\Role;
use LaravelUi5\Core\Attributes\SemanticLink;
use LaravelUi5\Core\Attributes\SemanticObject;
use LaravelUi5\Core\Enums\AbilityType;
use LaravelUi5\Core\Enums\ArtifactType;
use LaravelUi5\Core\Ui5\Contracts\ReportActionInterface;
use LaravelUi5\Core\Ui5\Contracts\SluggableInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ActionInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5RegistryInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ReportInterface;
use LogicException;
use ReflectionClass;
use ReflectionException;

class Ui5Registry implements Ui5RegistryInterface
{
    protected array $modules = [];
    protected array $artifacts = [];
    protected array $namespaceToModule = [];
    protected array $slugs = [];
    protected array $roles = [];
    protected array $abilities = [];

    protected array $objects = [];
    protected array $links = [];

    /**
     * @throws ReflectionException
     */
    public function __construct()
    {
        $config = config('ui5');

        $modules = $config['modules'] ?? [];
        foreach ($modules as $slug => $moduleClass) {

            /** @var Ui5ModuleInterface $module */
            $module = new $moduleClass($slug);

            $this->registerRoles($module);
            $this->registerSemanticObject($module);

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
        $this->registerSemanticLinks();

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
     * Extract all role definitions from a given Ui5Module class.
     *
     * @param Ui5ModuleInterface $module
     *
     * @throws LogicException If a role is declared twice
     */
    protected function registerRoles(Ui5ModuleInterface $module): void
    {
        $ref = new ReflectionClass($module);
        $attributes = $ref->getAttributes(Role::class);
        foreach ($attributes as $attribute) {
            /** @var Role $role */
            $role = $attribute->newInstance();
            if (array_key_exists($role->title, $this->roles)) {
                $namespace = $module->getArtifactRoot()->getNamespace();
                throw new LogicException("Role '$role->title' declared in module '$namespace' already exists.");
            }
            $this->roles[$role->title] = $role->description ?? '';
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
     * @throws ReflectionException
     */
    protected function registerArtifact(Ui5ArtifactInterface $artifact, ?string $moduleSlug): void
    {
        $namespace = $artifact->getNamespace();
        $this->artifacts[$namespace] = $artifact;

        if (null !== $moduleSlug) {
            $this->namespaceToModule[$namespace] = $moduleSlug;
        }

        $this->discoverAbilities($artifact);

        if ($artifact instanceof SluggableInterface) {
            $urlKey = ArtifactType::urlKeyFromArtifact($artifact);
            $this->slugs[$urlKey] = $artifact;
        }
    }

    /**
     * Discover and register Ability attributes defined on Ui5 artifacts or report actions.
     *
     * - Only one Ability per class is allowed.
     * - Type::Use and Type::See are not permitted on backend classes.
     * - Type::Act must appear only on Ui5ActionInterface or ReportActionInterface.
     *
     * @throws ReflectionException|LogicException
     */
    protected function discoverAbilities(Ui5ArtifactInterface|ReportActionInterface $artifact): void
    {
        $ref = new ReflectionClass($artifact);

        // The module root (i.e. app/lib) defines the canonical namespace grouping for Abilities
        $namespace = $artifact->getModule()->getArtifactRoot()->getNamespace();
        $attributes = $ref->getAttributes(Ability::class);

        if (count($attributes) > 1) {
            throw new LogicException(
                sprintf(
                    'Multiple Ability attributes found on [%s]. Each class may define only one Ability.',
                    $ref->getName()
                )
            );
        }

        if (count($attributes) === 1) {
            /** @var Ability $ability */
            $ability = $attributes[0]->newInstance();

            if ($ability->type === AbilityType::Use) {
                throw new LogicException(sprintf(
                    'Invalid ability declaration: [%s] uses type [Use], which is reserved for UI visibility. Move this definition to your manifest.json file.',
                    $ability->name
                ));
            }

            if ($ability->type === AbilityType::Act && !($artifact instanceof Ui5ActionInterface || $artifact instanceof ReportActionInterface)) {
                throw new LogicException(sprintf(
                    'Ability [%s] of type [Act] must be declared on an executable artifact, found on [%s].',
                    $ability->name,
                    get_class($artifact)
                ));
            }

            $this->abilities[$namespace][$ability->name] = [
                'type' => $ability->type->name,
                'role' => $ability->role,
                'note' => $ability->note,
            ];

            if ($artifact instanceof Ui5ReportInterface) {
                foreach ($artifact->getActions() as $action) {
                    $this->discoverAbilities($action);
                }
            }
        }
    }

    /**
     * Detects and registers a module's declared SemanticObject.
     *
     * @throws ReflectionException|LogicException
     */
    protected function registerSemanticObject(Ui5ModuleInterface $module): void
    {
        $ref = new ReflectionClass($module);
        $attributes = $ref->getAttributes(SemanticObject::class);

        if (count($attributes) === 0) {
            return; // Module may not declare a semantic object
        }

        if (count($attributes) > 1) {
            throw new LogicException(sprintf(
                'Multiple SemanticObject attributes found on module [%s]. Each module may declare only one.',
                $ref->getName()
            ));
        }

        /** @var SemanticObject $semantic */
        $semantic = $attributes[0]->newInstance();

        // Validate required fields
        if (empty($semantic->model) || empty($semantic->name) || empty($semantic->routes)) {
            throw new LogicException(sprintf(
                'Invalid SemanticObject definition in [%s]. Parameters $model, $name, and $routes are required.',
                $ref->getName()
            ));
        }

        // Ensure at least one route
        if (count($semantic->routes) < 1) {
            throw new LogicException(sprintf(
                'SemanticObject [%s] must define at least one route intent.',
                $semantic->name
            ));
        }

        // Prevent duplicate model ownership
        if (isset($this->objects[$semantic->model])) {
            throw new LogicException(sprintf(
                'Model [%s] is already registered as a SemanticObject by [%s].',
                $semantic->model,
                $this->objects[$semantic->model]['name']
            ));
        }

        $slug = $module->getSlug();

        $this->objects[$semantic->model] = [
            'module' => $slug,
            'name' => $semantic->name,
            'model' => $semantic->model,
            'routes' => $semantic->routes,
            'icon' => $semantic->icon,
        ];
    }

    /**
     * Performs the second discovery pass to resolve SemanticLink attributes.
     *
     * Scans only models registered as SemanticObjects and validates that each link
     * points to another registered SemanticObject model.
     *
     * @throws ReflectionException|LogicException
     */
    protected function registerSemanticLinks(): void
    {
        if (empty($this->objects)) {
            return; // nothing to process
        }

        foreach ($this->objects as $slug => $object) {
            $ref = new ReflectionClass($slug);

            foreach ($ref->getMethods() as $method) {
                foreach ($method->getAttributes(SemanticLink::class) as $attribute) {
                    /** @var SemanticLink $link */
                    $link = $attribute->newInstance();

                    // Validation: target model must exist as a registered semantic object
                    if (!isset($this->objects[$link->model])) {
                        throw new LogicException(sprintf(
                            'SemanticLink on [%s::%s] points to unknown model [%s]. ' .
                            'Target must be declared as a SemanticObject.',
                            $slug,
                            $method->getName(),
                            $link->model
                        ));
                    }

                    $this->links[$slug][] = $link->model;
                }
            }
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

    /** -- manifest.json facing -------------------------------------------- */
    public function resolveIntents(string $slug): array
    {
        $sourceModel = null;
        foreach ($this->objects as $model => $meta) {
            if ($meta['module'] === $slug) {
                $sourceModel = $model;
                break;
            }
        }

        if (!$sourceModel) {
            return []; // module has no semantic object → no intents to expose
        }

        $referencingModels = [];
        foreach ($this->links as $fromModel => $targets) {
            foreach ($targets as $toModel) {
                if ($toModel === $sourceModel) {
                    $referencingModels[] = $fromModel;
                }
            }
        }

        $intents = [];
        foreach ($referencingModels as $model) {
            $semantic = $this->objects[$model];
            if (!$semantic) {
                continue;
            }

            $objectName = $semantic['name'];
            foreach ($semantic['routes'] as $intent => $route) {
                $intents[$objectName][$intent] = [
                    'label' => $route['label'] ?? $intent,
                    'icon' => $route['icon'] ?? null,
                ];
            }
        }

        return $intents;
    }

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

    /** -- Client facing --------------------------------------------------- */
    public function introspect(): array
    {
        return [
            'modules' => $this->modules,
            'artifacts' => $this->artifacts,
            'namespaceToModule' => $this->namespaceToModule,
            'slugs' => $this->slugs,
            'roles' => $this->roles,
            'abilities' => $this->abilities,
            'objects' => $this->objects,
            'links' => $this->links,
        ];
    }
}
