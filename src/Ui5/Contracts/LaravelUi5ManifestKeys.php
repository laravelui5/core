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
        ];
    }
}
