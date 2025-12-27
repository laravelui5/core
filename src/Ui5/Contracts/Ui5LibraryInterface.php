<?php

namespace LaravelUi5\Core\Ui5\Contracts;

/**
 * Marker interface for UI5 libraries.
 *
 * Libraries contain reusable controls, message bundles, and preload scripts.
 * They do not expose routing or visible UI components. Registered libraries
 * can be resolved via the Ui5Registry and served statically via Laravel routes.
 */
interface Ui5LibraryInterface extends Ui5ArtifactInterface, HasAssetsInterface, VendorTaggedInterface
{
    /**
     * Returns the UI5 source associated with this library, if available.
     *
     * The source represents the original UI5 library project from which this
     * library was generated and provides access to introspection data such as
     * the library descriptor, dependencies, framework metadata and build
     * information.
     *
     * A source is typically available during development and SDK usage.
     * In runtime or cached environments, this method may return null if the
     * library is running in a packaged or self-contained state.
     *
     * @return Ui5LibrarySource|null The associated UI5 library source, or null if not available.
     */
    public function getSource(): ?Ui5LibrarySource;
}
