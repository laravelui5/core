<?php

namespace LaravelUi5\Core\Infrastructure\Contracts;

use LogicException;

/**
 * Manages UI5 source overrides defined in .ui5-sources.php.
 *
 * This service is the single authority for reading and writing
 * workspace source overrides. It is used by:
 *
 * - Ui5SourceStrategyResolver (read-only)
 * - ui5:app / ui5:lib commands (write access)
 *
 * The store guarantees that:
 * - all paths are absolute and valid directories
 * - keys are fully qualified module class names
 * - invalid entries never reach consumers
 */
interface Ui5SourceOverrideStoreInterface
{
    /**
     * Returns all resolved source overrides.
     *
     * @return array<class-string, string>
     *         Map of module class → absolute source path
     */
    public function all(): array;

    /**
     * Returns the source override path for a module, if present.
     *
     * @param class-string $moduleClass
     */
    public function get(string $moduleClass): ?string;

    /**
     * Adds or updates a source override for a module.
     *
     * Implementations MUST:
     * - normalize the path
     * - validate that it exists and is a directory
     * - persist the change atomically
     *
     * @param class-string $moduleClass
     * @param string $srcPath Absolute path to the workspace project directory
     *
     * @throws LogicException if the path is invalid
     */
    public function put(string $moduleClass, string $srcPath): void;
}
