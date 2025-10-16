<?php

namespace LaravelUi5\Core\Ui5;

use LaravelUi5\Core\Attributes\Parameter;
use LaravelUi5\Core\Enums\ParameterSource;
use LaravelUi5\Core\Exceptions\InvalidHttpMethodActionException;
use LaravelUi5\Core\Exceptions\InvalidParameterSourceException;
use LaravelUi5\Core\Ui5\Contracts\LaravelUi5ManifestInterface;
use LaravelUi5\Core\Ui5\Contracts\LaravelUi5ManifestKeys;
use LaravelUi5\Core\Ui5\Contracts\ParameterizableInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5RegistryInterface;
use ReflectionClass;
use RuntimeException;

/**
 * Base class for building a `laravel.ui5` manifest fragment.
 *
 * Automatically provides core sections (actions, reports, routes, meta)
 * and allows apps to augment the manifest via `augmentFragment()`.
 *
 * All keys must be defined in LaravelUi5ManifestKeys. Unknown keys will throw.
 */
abstract class AbstractLaravelUi5Manifest implements LaravelUi5ManifestInterface
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
    public function getLaravelUi5Fragment(string $module): array
    {
        $core = [
            LaravelUi5ManifestKeys::ACTIONS => $this->buildActions($module),
            LaravelUi5ManifestKeys::REPORTS => $this->buildReports($module),
            LaravelUi5ManifestKeys::ROUTES => $this->buildRoutes($module),
            LaravelUi5ManifestKeys::INTENTS => $this->buildIntents($module),
            LaravelUi5ManifestKeys::META => $this->buildMeta($module),
        ];

        $fragment = array_merge($core, $this->enhanceFragment($module));

        $unknownKeys = array_diff(array_keys($fragment), LaravelUi5ManifestKeys::all());
        if (!empty($unknownKeys)) {
            throw new RuntimeException(
                'Unknown manifest key(s) in laravel.ui5 fragment: ' . implode(', ', $unknownKeys)
            );
        }

        return array_filter($fragment, fn($value) => !empty($value));
    }

    /**
     * Optional hook to extend the manifest with abilities, roles, settings, etc.
     *
     * Override this in your subclass to provide domain-specific config.
     *
     * @return array<string, mixed>
     */
    abstract protected function enhanceFragment(string $module): array;

    /**
     * Returns the list of backend actions provided by this app.
     *
     * Override if needed.
     *
     * @return array<string, array{method: string, url: string}>
     */
    protected function buildActions(string $module): array
    {
        if (!$this->registry->hasModule($module)) {
            return [];
        }

        $resolved = $this->registry->getModule($module);

        $actions = [];
        foreach ($resolved->getActions() as $action) {
            if (!$action->getMethod()->isValidUi5ActionMethod()) {
                throw new InvalidHttpMethodActionException($action->getNamespace(), $action->getMethod()->label());
            }

            $uri = collect($this->getPathParameters($action->getHandler()))
                ->map(fn(string $parameter) => "/{{$parameter}}")
                ->implode('');

            $actions[$action->getSlug()] = [
                'method' => $action->getMethod()->label(),
                'url' => "/ui5/{$this->registry->slugFor($action)}{$uri}"
            ];
        };

        return $actions;
    }

    protected function getPathParameters(ParameterizableInterface $target): array
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

    /**
     * Returns the list of report definitions provided by this app.
     *
     * @return array<string, mixed>
     */
    protected function buildReports(string $module): array
    {
        return [];
    }

    /**
     * Returns commonly used routes like logout, login, profile.
     *
     * @return array<string, string>
     */
    protected function buildRoutes(string $module): array
    {
        return array_map(
            fn($name) => route($name),
            config('ui5.routes', [])
        );
    }

    protected function buildIntents(string $module): array
    {
        return $this->registry->resolveIntents($module);
    }

    /**
     * Returns static metadata like version, client, branding flags etc.
     *
     * @return array<string, mixed>
     */
    protected function buildMeta(string $module): array
    {
        return [
            'generator' => 'LaravelUi5 Core',
        ];
    }

}
