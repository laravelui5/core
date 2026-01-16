<?php

namespace LaravelUi5\Core\Ui5\Capabilities;

use LaravelUi5\Core\Ui5\Contracts\Ui5RegistryInterface;

/**
 * Allows a module or application to contribute configuration data
 * to the global LaravelUi5 Shell layer.
 *
 * The Shell represents the UI container that wraps all UI5 apps.
 * It provides global interactions such as navigation overlays,
 * help panels, search interfaces, command palettes, and keyboard
 * shortcuts. Unlike app-local UI elements, Shell components are
 * rendered independently from the active UI5 component and are
 * therefore defined at the platform level.
 *
 * Manifest fragments contributed through this interface are placed
 * under the top-level key LaravelUi5ManifestKeys::SHELL.
 *
 * Only modules implementing this interface can influence Shell
 * configuration. The Core does not impose a schema beyond the
 * requirement that the returned data must be serializable to JSON.
 *
 * Typical use cases include:
 * - defining keyboard triggers for Navigation, Search, Help
 * - enabling or configuring overlays rendered outside the app root
 * - contributing global command-palette entries
 * - registering static Shell UI elements (e.g. minimal visual cues)
 * - platform-wide availability metadata (enable/disable per module)
 *
 * The returned array must follow the structure expected by the
 * corresponding Shell implementation on the frontend.
 */
interface Ui5ShellFragmentInterface
{
    /**
     * Builds and returns the Shell fragment for this module.
     *
     * The returned array is inserted under the "shell" manifest key
     * and merged with the standard LaravelUi5 manifest structure.
     * Implementations must ensure that the fragment is compatible
     * with the Shell's configuration schema and does not contain any
     * unknown top-level keys outside the "shell" namespace.
     *
     * @return array<string, mixed> The Shell fragment to be added to the manifest.
     */
    public function buildShellFragment(Ui5RegistryInterface $registry, string $namespace): array;
}
