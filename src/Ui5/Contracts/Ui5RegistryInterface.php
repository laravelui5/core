<?php

namespace LaravelUi5\Core\Ui5\Contracts;

/**
 * The Ui5Registry is a read-only lookup service for all registered UI5 modules
 * and artifacts within the LaravelUi5 ecosystem.
 *
 * Its primary role is to provide fast, consistent and conflict-free resolution
 * of module and artifact classes at runtime.
 *
 * Responsibilities:
 * - Provide routing-safe access to modules by slug (e.g., "users")
 * - Provide rendering-safe access to artifacts by namespace (e.g., "io.pragmatiqu.users.cards.summary")
 *
 * System rules enforced:
 * - Every module must have a unique slug
 * - Every artifact must have a globally unique namespace
 * - Artifacts are only accessible via their registered modules or directly via namespace
 *
 * Example use cases:
 * - Resolve the module for an incoming route like `/ui5/app/users/...`
 * - Inject a card component via `<x-ui5-element id="io.pragmatiqu.users.cards.summary" />`
 * - Generate manifest.json for a UI5 app
 * - Dispatch an API action based on a module slug and action name
 *
 * TODO Add those for more intend driven design:
 * public function getOrFail(string $namespace): Ui5ArtifactInterface;
 *
 * public function fromSlugOrFail(string $slug): Ui5ArtifactInterface;
 *
 * public function getModuleOrFail(string $slug): Ui5ModuleInterface;
 */
interface Ui5RegistryInterface
{
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
     * Resolves an array of resource roots (namespace => URL) from an array of namespaces.
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
