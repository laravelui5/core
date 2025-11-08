<?php

namespace LaravelUi5\Core\Ui5\Contracts;

use LaravelUi5\Core\Enums\AbilityType;
use LaravelUi5\Core\Enums\ArtifactType;

/**
 * The `Ui5Registry` is the central coordination and introspection service
 * of the LaravelUi5 ecosystem. It provides a unified API to discover, inspect,
 * and resolve all UI5-related artifacts, modules, and semantic metadata
 * within a Laravel application.
 *
 * It provides
 *  - Configuration based declaration of modules and artifacts
 *  - Metadata introspection on roles, abilities, settings and objects
 *  - Fast runtime resolution of modules and artifacts
 *
 * System rules enforced
 * - Every module must have a unique slug
 * - Every artifact must have a globally unique namespace
 * - Artifacts are only accessible via their registered modules or directly via namespace
 *
 * Example use cases
 * - Resolve the module for an incoming route like `/ui5/app/users/...`
 * - Inject a card component via `<x-ui5-element id="io.pragmatiqu.users.cards.summary" />`
 * - Generate manifest.json for a UI5 app
 * - Dispatch an API action based on a module slug and action name
 */
interface Ui5RegistryInterface
{
    /** -- Lookup Layer ---------------------------------------------------- */

    /**
     * Returns the module with the given slug, or null if not found.
     */
    public function getModule(string $slug): ?Ui5ModuleInterface;

    /**
     * Checks if a module with the given slug exists.
     */
    public function hasModule(string $slug): bool;

    /**
     * Returns all registered modules.
     *
     * @return Ui5ModuleInterface[]
     */
    public function modules(): array;

    /**
     * Returns the artifact with the given namespace, or null if not found.
     */
    public function get(string $namespace): ?Ui5ArtifactInterface;

    /**
     * Checks if an artifact with the given namespace is registered.
     */
    public function has(string $namespace): bool;

    /**
     * Returns all registered artifacts.
     *
     * @return Ui5ArtifactInterface[]
     */
    public function all(): array;

    /** -- Introspection Layer --------------------------------------------- */

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

    /** -- Runtime Layer --------------------------------------------------- */

    /**
     * Returns the artifact by its slug (as used in routing or URLs), or null if not found.
     */
    public function fromSlug(string $slug): ?Ui5ArtifactInterface;

    /**
     * Returns the full slug (e.g. "app/offers") for the given artifact.
     */
    public function slugFor(Ui5ArtifactInterface $artifact): ?string;

    /**
     * Resolves a full UI5 resource path (e.g. for use in resourceroots) based on namespace.
     */
    public function resolve(string $namespace): ?string;

    /**
     * Resolves a list of navigation intents in the semantic graph.
     *
     * @param string $slug The module slug
     * @return array<string, array<string, array>>
     */
    public function resolveIntents(string $slug): array;

    /**
     * Resolves an array of resource roots (namespace => URL) from an array of namespaces.
     *
     * @param array<int,string> $namespaces
     * @return array<string,string>
     */
    public function resolveRoots(array $namespaces): array;

    /**
     * Returns the slug of the module that owns the given artifact namespace.
     */
    public function namespaceToModuleSlug(string $namespace): ?string;

    /**
     * Returns the slug of the module that owns the given artifact class name.
     */
    public function artifactToModuleSlug(string $class): ?string;
}
