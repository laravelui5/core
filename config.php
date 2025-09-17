<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OpenUI5 Framework Version
    |--------------------------------------------------------------------------
    |
    | This value defines the default OpenUI5 version used by all UI5 apps,
    | unless explicitly overridden within the app configuration.
    |
    | It is applied during runtime resolution and when scaffolding new
    | self-contained or workspace-based applications.
    |
    | Use a valid version tag (e.g., "1.120.5") from the official UI5 CDN.
    |
    */
    'version' => '1.136.1',

    /*
    |--------------------------------------------------------------------------
    | UI5 App Routing
    |--------------------------------------------------------------------------
    |
    | This array allows you to optionally define Laravel route names that will
    | be exposed to the UI5 application manifest. This is useful for
    | authentication and navigation purposes.
    |
    | The specified entries will be automatically resolved and injected into
    | the "sap.ui5.config" section of the generated manifest.json.
    |
    | Example (if desired):
    |
    | 'routes' => [
    |     'login'   => 'user.login',      // becomes: '/user/login'
    |     'logout'  => 'user.logout',     // becomes: '/user/logout'
    |     'profile' => 'user.profile',    // e.g. '/user/me'
    |     'home'    => 'dashboard.index', // e.g. '/dashboard'
    | ],
    |
    | Note: The values must be valid Laravel route names.
    |       During manifest generation, they will be resolved using
    |       Laravel’s `route(...)` helper (relative or absolute URLs).
    |
    */
    'routes' => [],

    /*
    |--------------------------------------------------------------------------
    | UI5 Registry Implementation
    |--------------------------------------------------------------------------
    |
    | This option controls which implementation of the Ui5RegistryInterface is
    | used by the LaravelUi5 system. By default, the in-memory registry is
    | used (suitable for development). In production, you should consider
    | switching to the cached registry for better performance.
    |
    | Available values:
    | - \LaravelUi5\Core\Ui5Registry           (default, non-cached)
    | - \LaravelUi5\Core\CachedUi5Registry     (uses compiled cache)
    |
    | To (re)generate the artifact cache, run:
    |   php artisan ui5:cache
    |
    */
    'registry' => \LaravelUi5\Core\Ui5\Ui5Registry::class,

    /*
    |--------------------------------------------------------------------------
    | Registered UI5 Modules
    |--------------------------------------------------------------------------
    |
    | This array maps a route-level "module" slug to its corresponding module class.
    | Each module represents a cohesive functional unit within the application,
    | containing either a UI5 application or a UI5 library, and optionally
    | associated artifacts like cards, KPIs, reports, tiles, and actions.
    |
    | The key is used as the first route segment in URLs (e.g., /app/users/...).
    | It must be unique across all modules to ensure correct routing and reverse lookup.
    |
    | ⛔ WARNING: This configuration is critical to the correct resolution of modules,
    | artifact routing, and namespace disambiguation. Only experienced users should
    | make changes here, as incorrect mappings will break route resolution and
    | lead to ambiguous artifact lookups.
    |
    | Example:
    | 'users' => \Vendor\Package\UsersModule::class
    |
    */
    'modules' => [
        'core' => \LaravelUi5\Core\CoreModule::class,
        'dashboard' => \LaravelUi5\Core\DashboardModule::class,
        'report' => \LaravelUi5\Core\ReportModule::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Registered UI5 Dashboards
    |--------------------------------------------------------------------------
    |
    | Dashboards are standalone UI5 XML fragments used for tile-based overviews,
    | often rendered in the shell container or as entry points in business flows.
    |
    | They are not bound to a specific module and are resolved by global namespace.
    | Each dashboard must implement the Ui5DashboardInterface and declare a unique
    | JavaScript namespace for reverse lookup and permission control.
    |
    | Example:
    | \Vendor\Package\Dashboards\OffersDashboard::class
    |
    */
    'dashboards' => [
    ],

    /*
    |--------------------------------------------------------------------------
    | Registered UI5 Reports
    |--------------------------------------------------------------------------
    |
    | Reports are standalone UI5 artifacts representing business evaluations
    | with an optional selection mask, a rendered result view, and follow-up
    | actions such as exports or workflow triggers.
    |
    | Reports are not bound to a specific module and are resolved by global
    | namespace. Each report must implement the Ui5ReportInterface and
    | register a unique urlKey and JavaScript namespace for reverse lookup
    | and permission control.
    |
    | Example:
    | \Vendor\Package\Reports\Hours\Report::class
    |
    */
    'reports' => [
    ],

    /*
    |--------------------------------------------------------------------------
    | Active System
    |--------------------------------------------------------------------------
    |
    | This value is controlled via the SYSTEM=... entry in your .env file and
    | determines which configuration (e.g., middleware setup, proxy target)
    | should be applied for the current environment.
    |
    */
    'active' => env('SYSTEM', 'PRO'),

    /*
    |--------------------------------------------------------------------------
    | System-Specific Configurations
    |--------------------------------------------------------------------------
    |
    | Depending on the active environment (e.g., DEV, QA, PROD), different
    | middleware definitions can be applied. Middleware is automatically
    | loaded for all OData endpoints, provided your routing is configured
    | accordingly.
    |
    */
    'systems' => [

        'DEV' => [
            'middleware' => [
                'web'
            ],
        ],

        'QS' => [
            'middleware' => [
                'web', 'auth.odata'
            ],
        ],

        'PRO' => [
            'middleware' => [
                'web', 'auth.odata'
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Lodata Configuration (Shadow)
    |--------------------------------------------------------------------------
    |
    | These settings control the behavior of the Lodata OData engine
    | within the LaravelUi5 environment. This acts as a shadow configuration
    | that overrides the default `lodata.php` file and ensures that only
    | intentional and compatible features are used.
    |
    */
    'lodata' => [

        /**
         * The route prefix to use, by default, mounted at http://localhost:8080/odata
         * but can be moved and renamed as required.
         */
        'prefix' => env('LODATA_PREFIX', 'odata'),

        /*
         * Whether this service should allow data modification requests.
         * Enabled by default to prevent unintended data modification.
         */
        'readonly' => true,

        /*
         * Set this to true if you want to use Laravel authorization
         * gates for your OData requests.
         */
        'authorization' => false,

        /*
         * This is an OData concept to group your data model according to a globally
         * unique namespace. Some clients may use this information for display purposes.
         */
        'namespace' => env('LODATA_NAMESPACE', 'com.example.odata'),

        /*
         * Whether to use streaming JSON responses by default.
         * @link https://docs.oasis-open.org/odata/odata-json-format/v4.01/odata-json-format-v4.01.html#sec_PayloadOrderingConstraints
         */
        'streaming' => true,

        /*
         * The name of the Laravel disk to use to store asynchronously processed requests.
         * In a multi-server shared hosting environment, all hosts should be able to access this disk
         */
        'disk' => env('LODATA_DISK', 'local'),

        /*
         * Configuration for server-driven pagination
         */
        'pagination' => [
            /**
             * The maximum page size this service will return, null for no limit
             */
            'max' => null,

            /**
             * The default page size to use if the client does not request one, null for no default
             */
            'default' => 200,
        ],

        /*
         * Configuration relating to auto-discovery
         */
        'discovery' => [
            /**
             * The cache store to use for discovered data
             */
            'store' => env('LODATA_DISCOVERY_STORE'),

            /**
             * How many seconds to cache discovered data for. Setting to null will cache forever.
             */
            'ttl' => env('LODATA_DISCOVERY_TTL', 0),

            /*
             * The blacklist of property names that will not be added during auto-discovery
             */
            'blacklist' => [
                'password',
                'api_key',
                'api_token',
                'api_secret',
                'secret',
            ]
        ],

        /**
         * Configuration for multiple service endpoints
         */
        'endpoints' => [],
    ]
];
