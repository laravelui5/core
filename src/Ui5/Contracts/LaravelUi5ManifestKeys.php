<?php

namespace LaravelUi5\Core\Ui5\Contracts;

/**
 * Class LaravelUi5ManifestKeys
 *
 * Defines the list of reserved keys that are allowed inside the `laravel.ui5`
 * manifest section. These keys define the structure and semantics of LaravelUi5
 * metadata exposed to the UI5 frontend.
 *
 * This class serves as a central registry for all first-class manifest keys.
 * You can use these constants when building the manifest fragment in
 * LaravelUi5ManifestInterface implementations.
 *
 * The values are returned as strings and intended to be used as array keys
 * in JSON-encoded manifests.
 */
final class LaravelUi5ManifestKeys
{

    /**
     * Defines meta information about the current app, tenant, or environment.
     * Can include version info, license state, branding flags, etc.
     */
    public const string META = 'meta';

    /**
     * Defines route names that can be consumed by the UI5 frontend
     * (e.g., logout, profile, login)
     */
    public const string ROUTES = 'routes';

    /**
     * Defines available backend actions callable via LaravelUi5.call(...)
     *
     * @see Ui5ActionInterface
     */
    public const string ACTIONS = 'actions';

    /**
     * Defines available resource endpoints callable via LaravelUi5.get(...)
     */
    public const string RESOURCES = 'resources';

    /**
     * Defines navigation intents defined by semantic object graph.
     */
    public const string INTENTS = 'intents';

    /**
     * Defines settings exposed to the frontend.
     * Can include feature toggles, UI preferences, branding info, etc.
     */
    public const string SETTINGS = 'settings';

    /**
     * Defines a dedicated extension namespace for vendor-specific metadata.
     *
     * This key serves as a sandbox for SDKs, commercial add-ons, partner modules,
     * or other vendor packages that need to expose additional manifest information
     * without affecting the core manifest structure.
     *
     * The Core does not prescribe any internal schema for this section. All data
     * placed under this key must be considered vendor-owned and optional.
     *
     * Typical use cases include:
     * - custom feature descriptors
     * - vendor-specific configuration blocks
     * - commercial edition metadata
     * - integration markers for partner ecosystems
     */
    public const string SDK = 'sdk';

    /**
     * Defines configuration for the global LaravelUi5 Shell layer.
     *
     * This section configures the UI elements that wrap the active app, such as
     * navigation overlays, help panels, search interfaces, or other global
     * controls. The Shell is rendered independently of any specific UI5 app and
     * therefore requires its own top-level manifest structure.
     *
     * Only modules implementing Ui5ShellFragmentInterface may contribute to this
     * section. Its structure is strictly defined by the Core to ensure predictable
     * behavior across the entire runtime environment.
     *
     * Typical contents include:
     * - navigation overlay configuration
     * - help or guidance overlays
     * - global search and command interfaces
     * - keyboard shortcut mappings
     */
    public const string SHELL = 'shell';

    /**
     * Returns all known manifest keys in the order they are typically rendered.
     *
     * This list can be used to validate manifest fragments or for documentation
     * and introspection purposes.
     *
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            self::ACTIONS,
            self::ROUTES,
            self::META,
            self::RESOURCES,
            self::INTENTS,
            self::SETTINGS,
            self::SDK,
        ];
    }
}
