<?php

namespace LaravelUi5\Core\Ui5\Contracts;

use LogicException;

/**
 * Strategy interface for resolving and accessing UI5 source artifacts.
 *
 * A Ui5SourceStrategy encapsulates *how* UI5 resources for a given module
 * are located and accessed, depending on the module's origin.
 *
 * Typical implementations include:
 *  - Workspace-based strategies (live UI5 projects under development)
 *  - Package-based strategies (Composer-installed, self-contained modules)
 *
 * The strategy is resolved centrally (e.g. by the Ui5Registry) and is
 * responsible for:
 *  - determining the runtime UI5 resource path,
 *  - and, optionally, creating an introspection-capable Ui5Source instance.
 *
 * Important:
 * - The runtime path must always be resolvable and usable.
 * - Introspection may be expensive and is therefore optional.
 * - Consumers must not assume that introspection is available.
 */
interface Ui5SourceStrategyInterface
{
    /**
     * Returns the absolute filesystem path to the runtime UI5 resources
     * of the associated module.
     *
     * This path must point to a directory containing consumable UI5 artifacts
     * such as:
     *  - manifest.json
     *  - preload bundles
     *  - i18n files
     *
     * The returned path is guaranteed to exist and be readable.
     *
     * @return string Absolute path to runtime UI5 resources.
     */
    public function getSourcePath(): string;

    /**
     * Creates and returns a Ui5AppSource instance for introspection.
     *
     * This method MUST only be called if supportsAppIntrospection() returns true.
     * Implementations may perform filesystem access, parsing, or other
     * expensive operations.
     *
     * @param string $vendor The package vendor.
     *
     * @return Ui5AppSource Introspection-capable UI5 source object.
     * @throws LogicException If introspection is not supported.
     */
    public function createAppSource(string $vendor): Ui5AppSource;

    /**
     * Creates and returns a Ui5LibrarySource instance for introspection.
     *
     * This method MUST only be called if supportsLibraryIntrospection() returns true.
     * Implementations may perform filesystem access, parsing, or other
     * expensive operations.
     *
     * @return Ui5LibrarySource Introspection-capable UI5 source object.
     * @throws LogicException If introspection is not supported.
     */
    public function createLibrarySource(string $vendor): Ui5LibrarySource;
}
