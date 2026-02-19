<?php

namespace LaravelUi5\Core\Ui5\Contracts;

use LogicException;

/**
 * Interface Ui5RegistryInterface
 *
 * Build-time registry and introspection contract for the LaravelUi5 Core.
 *
 * The Ui5Registry is the authoritative source of truth for all UI5-related
 * artifacts declared in a Laravel application. It is responsible for
 * discovering, instantiating, and indexing UI5 modules and artifacts
 * based on configuration and PHP attributes.
 *
 * The registry operates exclusively at build time (or during application
 * bootstrapping) and is intentionally reflection-heavy. Its primary purpose
 * is to provide normalized, deterministic metadata for:
 *
 *  - runtime cache generation
 *  - manifest and resource root assembly
 *  - documentation and inspection tooling
 *
 * The registry does NOT:
 *  - perform authorization
 *  - interpret semantic meaning
 *  - resolve user intents
 *  - make runtime decisions
 *
 * In Core 2.0, the registry is strictly technical and URI-oriented.
 * Semantic concerns (navigation meaning, intent declaration, authorization)
 * are explicitly handled by the SDK layer.
 *
 * Responsibilities:
 *  - Discover UI5 modules from configuration
 *  - Discover UI5 artifacts via module registration
 *  - Guarantee uniqueness of module slugs and artifact namespaces
 *  - Provide deterministic lookup structures for cache generation
 *
 * System guarantees:
 *  - Each module has exactly one unique slug
 *  - Each artifact has a globally unique namespace
 *  - Artifact type (app, lib, action, report, …) is stable and deterministic
 *
 * Typical consumers:
 *  - ui5:cache command
 *  - build-time manifest generators
 *  - tooling and diagnostics
 *
 * @package LaravelUi5\Core\Ui5\Contracts
 */
interface Ui5RegistryInterface
{
    /**
     * Returns all registered modules.
     *
     * @return array<string, Ui5ModuleInterface>
     */
    public function modules(): array;

    /**
     * Returns the module instance for the given slug, or null if not found.
     *
     * @param string $namespace The URL slug to identify the module (from `config/ui5.php > modules`)
     * @return Ui5ModuleInterface|null The instantiated module, or null if not found
     */
    public function getModule(string $namespace): ?Ui5ModuleInterface;

    /**
     * Returns the registered artifact instance for the given class name.
     *
     * @param class-string<Ui5ModuleInterface> $class
     *
     * @throws LogicException if the given artifact class
     *                        is not registered in this registry.
     */
    public function getModuleByClass(string $class): Ui5ModuleInterface;

    /**
     * Returns all registered artifacts across all modules.
     *
     * @return array<string, Ui5ArtifactInterface>
     */
    public function artifacts(): array;

    /**
     * Returns the artifact instance for the given namespace, or null if not found.
     *
     * @param string $namespace The fqn of the UI5 artifact
     * @return Ui5ArtifactInterface|null The instantiated artifact, or null if not found
     */
    public function getArtifact(string $namespace): ?Ui5ArtifactInterface;

    /**
     * Returns the registered artifact instance for the given class name.
     *
     * @param class-string<Ui5ArtifactInterface> $class
     *
     * @throws LogicException if the given artifact class
     *                        is not registered in this registry.
     */
    public function getArtifactByClass(string $class): Ui5ArtifactInterface;

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
     * Converts external URI path segments to canonical registry namespace.
     *
     * Example:
     *  - "io/pragmatiqu/partners" → "io.pragmatiqu.partners"
     *
     * This method centralizes URI-to-registry normalization logic and
     * avoids scattering string transformation rules across the codebase.
     *
     * @param string $namespace
     * @return string
     */
    public function pathToNamespace(string $namespace): string;

    /**
     * Converts canonical registry namespace to external URI path segments.
     *
     * Example:
     *   - "io.pragmatiqu.partners" → "io/pragmatiqu/partners"
     *
     *  This method centralizes registry-to-URI normalization logic and
     *  avoids scattering string transformation rules across the codebase.
     *
     * @param string $namespace
     * @return string
     */
    public function namespaceToPath(string $namespace): string;

    /**
     * Resolves a full public URL path for the given namespace
     *  (e.g. "/ui5/app/offers/1.0.0").
     *
     * @param string $namespace The fqn of the UI5 artifact
     * @return string|null The absolute path, or null if not found
     */
    public function resolve(string $namespace): ?string;

    /**
     * Resolves resource root URLs (namespace => URL) for multiple namespaces.
     *
     * @param array<int,string> $namespaces The fqn of the UI5 artifact (app or lib!)
     * @return array<string,string>
     */
    public function resolveRoots(array $namespaces): array;
}
