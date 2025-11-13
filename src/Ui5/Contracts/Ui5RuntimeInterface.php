<?php

namespace LaravelUi5\Core\Ui5\Contracts;

use LaravelUi5\Core\Enums\AbilityType;
use LaravelUi5\Core\Enums\ArtifactType;

/**
 * Interface Ui5RuntimeInterface
 *
 * Defines the runtime lookup contract for the LaravelUi5 ecosystem.
 * This interface provides a minimal, read-only API to efficiently
 * resolve modules, artifacts, and semantic navigation data.
 *
 * The runtime interface is designed for performance and determinism:
 * it supposes no reflection, scanning, or configuration merges.
 * Instead, it exposes lightweight lookups for runtime use cases such as
 * request routing, resource resolution, and intent navigation.
 *
 * Example use cases:
 *  - Resolve a module instance for an incoming route (e.g. `/ui5/app/users/...`)
 *  - Instantiate a UI5 artifact based on its namespace or slug
 *  - Generate runtime resource roots for a set of modules
 *  - Resolve cross-module navigation intents for deep links
 *
 * @package LaravelUi5\Core\Ui5\Contracts
 */
interface Ui5RuntimeInterface
{
    /**
     * Checks whether a module with the given slug exists.
     *
     * @param string $slug The URL slug to identify the module (from `config/ui5.php > modules`)
     * @return bool True if module for slug is known
     */
    public function hasModule(string $slug): bool;

    /**
     * Returns the module instance for the given slug, or null if not found.
     *
     * @param string $slug The URL slug to identify the module (from `config/ui5.php > modules`)
     * @return Ui5ModuleInterface|null The instantiated module, or null if not found
     */
    public function getModule(string $slug): ?Ui5ModuleInterface;

    /**
     * Checks whether an artifact with the given namespace is registered.
     *
     * @param string $namespace The fqn of the UI5 artifact
     * @return bool True if namespace for artifact is known
     */
    public function has(string $namespace): bool;

    /**
     * Returns the artifact instance for the given namespace, or null if not found.
     *
     * @param string $namespace The fqn of the UI5 artifact
     * @return Ui5ArtifactInterface|null The instantiated artifact, or null if not found
     */
    public function get(string $namespace): ?Ui5ArtifactInterface;

    /**
     * Returns the artifact instance for the given slug (as used in routing or URLs),
     * or null if not found.
     *
     * @param string $slug The URL slug to identify the artifact
     * @return Ui5ArtifactInterface|null The instantiated artifact, or null if not found
     */
    public function fromSlug(string $slug): ?Ui5ArtifactInterface;

    /**
     * Returns the canonical slug (e.g. "app/offers") for the given artifact.
     *
     * @param Ui5ArtifactInterface $artifact
     * @return string|null
     */
    public function slugFor(Ui5ArtifactInterface $artifact): ?string;

    /**
     * Resolves a full public URL path for the given namespace
     *  (e.g. "/ui5/app/offers/1.0.0").
     *
     * @param string $namespace The fqn of the UI5 artifact
     * @return string|null The absolute path, or null if not found
     */
    public function resolve(string $namespace): ?string;

    /**
     * Resolves all semantic navigation intents for the given module.
     *
     * @param string $slug The module slug
     * @return array<string, array<string, array>>
     */
    public function resolveIntents(string $slug): array;

    /**
     * Resolves resource root URLs (namespace => URL) for multiple namespaces.
     *
     * @param array<int,string> $namespaces The fqn of the UI5 artifact (app or lib!)
     * @return array<string,string>
     */
    public function resolveRoots(array $namespaces): array;
}
