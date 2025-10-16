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
     * Defines available backend actions callable via LaravelUi5.call(...)
     *
     * @see Ui5ActionInterface
     */
    public const ACTIONS = 'actions';

    /**
     * Defines available report endpoints (selection + output)
     *
     * @see Ui5ReportInterface
     */
    public const REPORTS = 'reports';

    /**
     * Defines route names that can be consumed by the UI5 frontend
     * (e.g., logout, profile, login)
     */
    public const ROUTES = 'routes';

    /**
     * Defines meta information about the current app, tenant, or environment.
     * Can include version info, license state, branding flags, etc.
     */
    public const META = 'meta';

    /**
     * Defines ability-level access rules for components and actions.
     * The structure is usually nested and app-specific.
     *
     * @example "see", "use", "act"
     */
    public const ABILITIES = 'abilities';

    /**
     * Defines the list of available user roles within the app or tenant.
     * Used for UI filtering, permission mapping, and documentation.
     */
    public const ROLES = 'roles';

    /**
     * Defines user- or tenant-specific settings exposed to the frontend.
     * Can include feature toggles, UI preferences, branding info, etc.
     */
    public const SETTINGS = 'settings';

    /**
     * Defines navigation intents defined by semantic object graph.
     */
    public const INTENTS = 'intents';

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
            self::REPORTS,
            self::ROUTES,
            self::META,
            self::ABILITIES,
            self::ROLES,
            self::SETTINGS,
            self::INTENTS,
        ];
    }
}
