<?php

namespace LaravelUi5\Core\Ui5;

use LaravelUi5\Core\Attributes\Parameter;
use LaravelUi5\Core\Contracts\ParameterizableInterface;
use LaravelUi5\Core\Enums\ParameterSource;
use LaravelUi5\Core\Exceptions\InvalidHttpMethodActionException;
use LaravelUi5\Core\Exceptions\InvalidModuleException;
use LaravelUi5\Core\Exceptions\InvalidParameterSourceException;
use LaravelUi5\Core\Ui5\Capabilities\LaravelUi5ManifestInterface;
use LaravelUi5\Core\Ui5\Capabilities\LaravelUi5ManifestKeys;
use LaravelUi5\Core\Ui5\Capabilities\Ui5ShellFragmentInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5RegistryInterface;
use ReflectionClass;

/**
 * Base class for building a `laravel.ui5` manifest fragment.
 *
 * Automatically provides core sections (actions, reports, routes, meta)
 * and allows apps to augment the manifest via `augmentFragment()`.
 *
 * All keys must be defined in LaravelUi5ManifestKeys. Unknown keys will throw.
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
            LaravelUi5ManifestKeys::VENDOR => $this->enhanceFragment($module),
            LaravelUi5ManifestKeys::SHELL => $this->buildShell($this->registry, $namespace),
        ];

        return array_filter($core, fn($value) => !empty($value));
    }

    /**
     * Optional hook to extend the manifest with vendor specific data.
     *
     * Override this in your subclass to provide domain-specific config.
     *
     * @return array<string, mixed>
     */
    abstract protected function enhanceFragment(string $module): array;

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
        };

        return $actions;
    }

    private function buildResources(Ui5ModuleInterface $module): array
    {
        $resources = [];
        foreach ($module->getResources() as $resource) {
            $provider = $resource->getProvider();

            if ($provider instanceof ParameterizableInterface) {
                $uri = collect($this->getPathParameters($provider))
                    ->map(fn(string $parameter) => "/{{$parameter}}")
                    ->implode('');

                $path = $this->registry->resolve($resource->getNamespace());

                $resources[$resource->getNamespace()] = [
                    'method' => 'GET',
                    'url' => "{$path}/{$uri}"
                ];
            }
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

    private function getPathParameters(ParameterizableInterface $target): array
    {
        $reflection = new ReflectionClass($target);
        $attributes = $reflection->getAttributes(Parameter::class);
        $parameters = [];
        foreach ($attributes as $attr) {
            /** @var Parameter $attribute */
            $attribute = $attr->newInstance();
            if (ParameterSource::Path === $attribute->source) {
                $parameters[] = $attribute->uriKey;
            } else {
                throw new InvalidParameterSourceException($attribute->name, $attribute->source->label());
            }
        }
        return $parameters;
    }
}
