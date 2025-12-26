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
    // no additional methods required
}
