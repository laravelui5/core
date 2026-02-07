<?php

namespace LaravelUi5\Core\Ui5;

use InvalidArgumentException;
use LaravelUi5\Core\Attributes\Parameter;
use LaravelUi5\Core\Exceptions\InvalidHttpMethodActionException;
use LaravelUi5\Core\Exceptions\InvalidModuleException;
use LaravelUi5\Core\Ui5\Capabilities\LaravelUi5ManifestInterface;
use LaravelUi5\Core\Ui5\Capabilities\LaravelUi5ManifestKeys;
use LaravelUi5\Core\Ui5\Capabilities\Ui5ShellFragmentInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5RegistryInterface;
use LogicException;
use ReflectionClass;

/**
 * Base class for building the `laravel.ui5` manifest fragment.
 *
 * This class defines the authoritative contract for how LaravelUi5 metadata
 * is exposed to the UI5 frontend.
 *
 * **Ownership vs. Contribution**
 *
 * The Core exclusively defines the available top-level manifest keys
 * (see LaravelUi5ManifestKeys). The overall structure of the manifest is
 * closed and strictly validated.
 *
 * Modules may contribute data to selected sections, but may never redefine
 * the structure or introduce new keys.
 *
 * **Aggregatable Sections**
 *
 * Only the following manifest sections allow module-level contributions:
 *
 * - ROUTES:
 *   Allows modules to expose additional named URLs (e.g. logout, legal pages,
 *   branding assets) that can be consumed by the UI5 frontend.
 *
 * - VENDOR:
 *   A free-form, vendor-owned namespace for optional or proprietary metadata.
 *   The Core does not impose any schema on this section.
 *
 * All other sections are owned and fully controlled by the Core or the SDK
 * and cannot be extended via this class.
 *
 * **Shell Configuration**
 *
 * The SHELL section is not contributed through this base class.
 * It is exclusively managed by the SDK and populated via dedicated
 * interfaces (e.g. Ui5ShellFragmentInterface).
 *
 * This ensures a stable and predictable shell behavior across all apps.
 *
 * **Extension Mechanism**
 *
 * Subclasses may override the contribution hook to provide additional
 * manifest entries for allowed sections only.
 *
 * Any attempt to contribute to non-aggregatable sections or to use unknown
 * manifest keys must result in an exception.
 *
 * Rule of thumb:
 * The Core defines structure.
 * Modules may contribute entries.
 * Visibility and semantics remain explicit.
 */
abstract class AbstractManifest implements LaravelUi5ManifestInterface
{

    public function __construct(protected Ui5RegistryInterface $registry)
    {
    }

    /**
     * Returns the complete, validated `laravel.ui5` manifest fragment.
     *
     * This includes core sections (actions, reports, routes, meta) and
     * any application-specific extensions provided by `augmentFragment()`.
     *
     * @param string $module the module slug
     *
     * @return array<string, mixed>
     */
    public function getFragment(string $module): array
    {
        $resolved = $this->registry->getModule($module);
        if (!$resolved) {
            throw new InvalidModuleException($module);
        }

        $namespace = $resolved->getArtifactRoot()->getNamespace();

        $core = [
            LaravelUi5ManifestKeys::META => $this->buildMeta(),
            LaravelUi5ManifestKeys::ROUTES => $this->buildRoutes(),
            LaravelUi5ManifestKeys::ACTIONS => $this->buildActions($resolved),
            LaravelUi5ManifestKeys::RESOURCES => $this->buildResources($resolved),
            LaravelUi5ManifestKeys::SETTINGS => $this->buildSettings($namespace),
            LaravelUi5ManifestKeys::SHELL => $this->buildShell($this->registry, $namespace),
        ];

        $contrib = $this->contributeFragment($module);

        $fragment = $this->mergeFragment($core, $contrib);

        return array_filter($fragment, fn($value) => !empty($value));
    }

    /**
     * Contribute additional entries to the `laravel.ui5` manifest fragment.
     *
     * This hook allows a module to provide vendor-owned metadata or to expose
     * additional named routes to the UI5 frontend.
     *
     * ---
     *
     * **Allowed Sections**
     *
     * Contributions are strictly limited to the following manifest sections:
     *
     * - ROUTES:
     *   Allows modules to expose additional named URLs (e.g. logout, legal pages,
     *   branding assets) that can be consumed by the UI5 frontend.
     *
     * - VENDOR:
     *   A vendor-owned namespace for arbitrary metadata.
     *
     * All other manifest sections are owned by the Core or the SDK and MUST NOT
     * be contributed to through this method.
     *
     * **Vendor Namespace Rules**
     *
     * Contributions to the VENDOR section MUST be namespaced by the contributing
     * module.
     *
     * The returned structure MUST follow this pattern:
     *
     * ```
     * [
     *   'vendor' => [
     *     '<module-name>' => [
     *       // vendor-owned data
     *     ]
     *   ]
     * ]
     * ```
     *
     * The module name (usually `$module` or an equivalent stable identifier)
     * acts as the namespace root for all vendor-specific data.
     *
     * This ensures that multiple vendors, SDK components, or partner modules
     * can safely contribute metadata without collisions.
     *
     * **Validation**
     *
     * - Only keys defined in {@see LaravelUi5ManifestKeys} are allowed.
     * - Contributions to non-aggregatable sections will result in an exception.
     * - Unknown or malformed keys will be rejected.
     *
     * Rule of thumb:
     * The Core defines structure.
     * Modules contribute entries within well-defined boundaries.
     *
     * @param string $module The name of the contributing module.
     *
     * @return array<string, mixed> A manifest fragment contribution.
     */
    abstract protected function contributeFragment(string $module): array;

    private function mergeFragment(array $core, array $contrib): array
    {
        foreach ($contrib as $key => $value) {
            if (! in_array($key, LaravelUi5ManifestKeys::all(), true)) {
                throw new InvalidArgumentException("Unknown manifest key [$key].");
            }

            if (! $this->isAggregatable($key)) {
                throw new LogicException("Manifest key [$key] does not allow contributions.");
            }

            $core[$key] = array_merge(
                $core[$key] ?? [],
                $value
            );
        }

        return array_filter($core, fn ($v) => !empty($v));
    }

    private function isAggregatable(string $key): bool
    {
        return in_array($key, [
            LaravelUi5ManifestKeys::ROUTES,
            LaravelUi5ManifestKeys::VENDOR,
        ], true);
    }

    /**
     * Returns static metadata like version, client, branding flags etc.
     *
     * @return array<string, mixed>
     */
    private function buildMeta(): array
    {
        return ['generator' => 'LaravelUi5 Core'] + config('ui5.meta', []);
    }

    /**
     * Returns commonly used routes like privacy, terms, login, logout.
     *
     * @return array<string, string>
     */
    private function buildRoutes(): array
    {
        return array_map(fn($name) => route($name), config('ui5.routes', []));
    }

    /**
     * Returns the list of backend actions provided by this app.
     *
     * Override if needed.
     *
     * @return array<string, array{method: string, url: string}>
     */
    private function buildActions(Ui5ModuleInterface $module): array
    {
        $actions = [];
        foreach ($module->getActions() as $action) {
            if (!$action->getMethod()->isValidUi5ActionMethod()) {
                throw new InvalidHttpMethodActionException($action->getNamespace(), $action->getMethod()->label());
            }

            $uri = collect($this->getPathParameters($action->getHandler()))
                ->map(fn(string $parameter) => "/{{$parameter}}")
                ->implode('');

            $path = $this->registry->resolve($action->getNamespace());

            $actions[$action->getNamespace()] = [
                'method' => $action->getMethod()->label(),
                'url' => "{$path}/{$uri}"
            ];
        }

        return $actions;
    }

    private function buildResources(Ui5ModuleInterface $module): array
    {
        $resources = [];
        foreach ($module->getResources() as $resource) {
            $provider = $resource->getProvider();

            $uri = collect($this->getPathParameters($provider))
                ->map(fn(string $parameter) => "/{{$parameter}}")
                ->implode('');

            $path = $this->registry->resolve($resource->getNamespace());

            $resources[$resource->getNamespace()] = [
                'method' => 'GET',
                'url' => "{$path}/{$uri}"
            ];
        }

        return $resources;
    }

    private function buildSettings(string $namespace): array
    {
        return collect($this->registry->settings($namespace))
            ->map(fn($setting) => $setting['default'])
            ->all();
    }

    private function buildShell(Ui5RegistryInterface $registry, string $namespace): array
    {
        if ($this instanceof Ui5ShellFragmentInterface) {
            return $this->buildShellFragment($registry, $namespace);
        }

        return [];
    }

    /** -- Helper ---------------------------------------------------------- */

    private function getPathParameters(object $target): array
    {
        $reflection = new ReflectionClass($target);
        $attributes = $reflection->getAttributes(Parameter::class);
        $parameters = [];
        foreach ($attributes as $attr) {
            /** @var Parameter $attribute */
            $attribute = $attr->newInstance();
            $parameters[] = $attribute->uriKey;
        }
        return $parameters;
    }
}
