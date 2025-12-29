<?php

namespace LaravelUi5\Core\Ui5\Contracts;

use LaravelUi5\Core\Introspection\App\Ui5AppSource;
use LaravelUi5\Core\Ui5\Capabilities\HasAssetsInterface;
use LaravelUi5\Core\Ui5\Capabilities\LaravelUi5ManifestInterface;
use LaravelUi5\Core\Ui5\Capabilities\VendorTaggedInterface;

/**
 * Interface Ui5AppInterface
 *
 * Defines a full-featured UI5 Application that is both a client-side OpenUI5 entry point
 * and a backend-accessible OData service. Implementors who want to offer oData endpoints
 * should extend the ServiceEndpointInterface required by `flat3/lodata`.
 *
 * Apps implementing this interface are registered via the Ui5Registry and can dynamically
 * provide their UI5 manifest data, routing, UI bootstrap configuration, and static assets.
 *
 * The Laravel side will render a default `index.blade.php` layout and inject metadata,
 * UI5 bootstrap attributes, resource roots, and any custom styles or inline scripts.
 *
 * @see Ui5ArtifactInterface
 * @see HasAssetsInterface
 * @see VendorTaggedInterface
 */
interface Ui5AppInterface extends Ui5ArtifactInterface, HasAssetsInterface, VendorTaggedInterface
{
    /**
     * Returns the UI5 source associated with this app.
     *
     * The source represents the original UI5 project (application) from which
     * this app was generated and provides access to introspection data such as
     * the manifest, dependencies, bootstrap configuration and i18n resources.
     *
     * @return Ui5AppSource The associated UI5 app source.
     */
    public function getSource(): Ui5AppSource;

    /**
     * Returns a key-value map of sap-ui bootstrap attributes
     * (e.g. theme, async, compatVersion, oninit, etc.).
     *
     * These will be injected into the UI5 bootstrap script tag.
     *
     * @return array<string, string>
     */
    public function getUi5BootstrapAttributes(): array;

    /**
     * Returns a list of all required resource roots for this app.
     * The keys are the JS namespaces.
     *
     * Example: ['io.pragmatiqu.portal', 'io.pragmatiqu.tools']
     *
     * @return array<string, string>
     */
    public function getResourceNamespaces(): array;

    /**
     * Optional inline JavaScript to be included in the <head> tag.
     *
     * Typically used for sap.ui.loader.config(...) blocks.
     *
     * @return string|null
     */
    public function getAdditionalHeadScript(): ?string;

    /**
     * Optional inline CSS styles to be included in the <head> tag.
     *
     * @return string|null
     */
    public function getAdditionalInlineCss(): ?string;

    /**
     * Returns the absolute path to the raw manifest.json file
     * as shipped by the frontend UI5 application.
     *
     * This method is used by the Laravel ManifestController to read
     * the complete manifest structure (`sap.app`, `sap.ui`, `sap.ui5`)
     * without having to reconstruct it from PHP.
     *
     * @return string Absolute filesystem path to manifest.json
     */
    public function getManifestPath(): string;

    /**
     * Returns Laravel-specific manifest data to be injected
     * under the `laravel.ui5` root key in the final manifest.json.
     *
     * This section may include definitions such as `abilities`, `actions`,
     * `settings`, `reports`, or other backend-managed frontend metadata.
     *
     * @return LaravelUi5ManifestInterface
     */
    public function getLaravelUiManifest(): LaravelUi5ManifestInterface;
}
