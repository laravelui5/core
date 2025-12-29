<?php

namespace LaravelUi5\Core\Ui5\Capabilities;

/**
 * Interface LaravelUi5ManifestInterface
 *
 * Defines the Laravel-specific data structure injected into
 * the `laravel.ui5` section of a UI5 application's manifest.json.
 *
 * This section is used to expose backend-side metadata to the
 * frontend runtime, such as role-based UI abilities, dynamic
 * action endpoints, report definitions, app-level settings,
 * and Laravel routes.
 *
 * Every UI5 App that wants to provide this section must implement
 * the Ui5AppInterface and return a valid LaravelUi5ManifestInterface.
 *
 * @see Ui5AppInterface::getLaravelUiManifest()
 */
interface LaravelUi5ManifestInterface
{
    /**
     * Converts the full manifest fragment into an array structure
     * suitable for injection under the `laravel.ui5` root node.
     *
     * This is the single method consumed by the ManifestController.
     *
     * @return array<string, mixed>
     */
    public function getFragment(string $module): array;
}
