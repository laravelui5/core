<?php

namespace LaravelUi5\Core\Ui5\Contracts;

use LaravelUi5\Core\Enums\ArtifactType;

/**
 * Interface Ui5RegistryInterface
 *
 * Defines the build-time introspection and coordination contract of the
 * LaravelUi5 ecosystem. The registry exposes a unified API to discover,
 * inspect, and reflect upon all UI5-related modules, artifacts, roles,
 * abilities, settings, and semantic objects declared in a Laravel application.
 *
 * The registry operates at development or build time and performs reflection
 * across modules and PHP attributes. It is the authoritative source for
 * generating cache files, documentation, and metadata used at runtime
 * by the {@see Ui5RuntimeInterface}.
 *
 * Responsibilities:
 *  - Discover modules and artifacts from configuration and attributes
 *  - Collect and normalize metadata (roles, abilities, settings, semantic objects)
 *  - Provide build-time data for the runtime cache generator (`ui5:cache`)
 *
 * System guarantees:
 *  - Every module has a unique slug
 *  - Every artifact has a globally unique namespace
 *  - Artifacts are addressable either through their module or namespace
 *
 * Example use cases:
 *  - Generate the runtime cache file via `php artisan ui5:cache`
 *  - Produce manifest.json files or capability maps for modules
 *  - Inspect declared roles, settings, or abilities for validation or documentation
 *
 * @package LaravelUi5\Core\Ui5\Contracts
 */
interface Ui5RegistryInterface extends Ui5RuntimeInterface
{
    /**
     * Returns all registered modules.
     *
     * @return Ui5ModuleInterface[]
     */
    public function modules(): array;

    /**
     * Returns all registered artifacts across all modules.
     *
     * @return Ui5ArtifactInterface[]
     */
    public function artifacts(): array;

    /**
     * Returns all roles declared across all modules via #[Role] attributes.
     *
     * @return array<string, array>
     */
    public function roles(): array;

    /**
     * Returns all registered abilities, grouped by namespace and ability type.
     *
     * The result reflects the normalized internal structure:
     * `$abilities[$namespace][$type->label()][$abilityName] = Ability`.
     *
     * - When `$namespace` is provided, abilities are limited to that artifact
     *   namespace (e.g. "io.pragmatiqu.offers").
     * - When `$type` is provided, only abilities of that `AbilityType`
     *   (e.g. `AbilityType::Act`) are returned.
     * - When both are null, all abilities across all namespaces and types
     *   are returned.
     *
     * Example:
     * ```php
     * $registry->abilities('io.pragmatiqu.reports', AbilityType::Act);
     * // → [ 'toggleLock' => Ability, 'exportPdf' => Ability, ... ]
     * ```
     *
     * @param string|null $namespace Optional artifact namespace to filter by.
     * @param ArtifactType|null $type Optional ability type to filter by.
     * @return array
     */
    public function abilities(?string $namespace = null, ?ArtifactType $type = null): array;

    /**
     * Returns all settings declared via #[Setting] attributes,
     * grouped by artifact namespace.
     *
     * - When `$namespace` is provided, only settings belonging to
     *   that namespace are returned.
     * - When `$namespace` is null, all settings across all registered
     *   artifacts are returned.
     *
     * The result reflects the normalized internal structure:
     * `$settings[$namespace][$settingName] = Setting`.
     *
     * Example:
     * ```php
     * $registry->settings('io.pragmatiqu.dashboard');
     * // → [ 'refreshInterval' => Setting, 'theme' => Setting, ... ]
     * ```
     *
     * @param string|null $namespace  Optional artifact namespace to filter by.
     * @return array
     */
    public function settings(?string $namespace = null): array;

    /**
     * Returns all semantic objects declared via #[SemanticObject] attributes.
     *
     * Each object entry describes a business entity and its
     * available routes or actions as defined in PHP attributes.
     *
     * Example structure:
     * [
     *   "User" => [
     *     "name" => "User",
     *     "module" => "users",
     *     "routes" => [
     *       "display" => ["label" => "Show", "icon" => "sap-icon://display"],
     *       "edit"    => ["label" => "Edit", "icon" => "sap-icon://edit"]
     *     ]
     *   ]
     * ]
     *
     * @return array<string, array>
     */
    public function objects(): array;
}
