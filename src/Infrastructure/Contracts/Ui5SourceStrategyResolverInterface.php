<?php

namespace LaravelUi5\Core\Infrastructure\Contracts;

use LogicException;
use ReflectionException;

/**
 * Resolves the appropriate Ui5SourceStrategy for a given UI5 module.
 *
 * The Ui5SourceStrategyResolver is responsible for deciding *how*
 * UI5 source metadata should be accessed for a module, based on
 * runtime context and filesystem layout.
 *
 * It encapsulates all resolution logic, including:
 * - workspace overrides (e.g. .ui5-sources.php)
 * - reflection-based module location
 * - package conventions (vendor resources)
 *
 * The resolver does NOT:
 * - read UI5 metadata itself
 * - create Ui5Source objects directly
 * - know anything about App vs Library consumers
 *
 * Instead, it returns a {@see Ui5SourceStrategyInterface} that
 * fully encapsulates the chosen access strategy.
 *
 * Typical resolution flow:
 *
 * 1. Check for workspace override (developer workspace)
 * 2. Fallback to package-based resolution via module class location
 * 3. Fail explicitly if no valid source location can be resolved
 *
 * This resolver is intended to be:
 * - used by Ui5Registry (Core & SDK)
 * - cached at registry level
 * - extended in the future (e.g. remote sources, prebuilt descriptors)
 */
interface Ui5SourceStrategyResolverInterface
{
    /**
     * Resolves the source strategy for the given module class.
     *
     * The module class does not need to be instantiated.
     * Resolution is based on class name, reflection, and
     * configured overrides.
     *
     * @param class-string $moduleClass Fully qualified module class name.
     * @return Ui5SourceStrategyInterface A concrete strategy (e.g. WorkspaceStrategy, PackageStrategy).
     *
     * @throws ReflectionException If the module class cannot be reflected.
     * @throws LogicException If no valid UI5 source location can be resolved.
     */
    public function resolve(string $moduleClass): Ui5SourceStrategyInterface;
}
