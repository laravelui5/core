<?php

namespace LaravelUi5\Core\Ui5\Contracts;

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
