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
    'version' => '1.136.8',

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
    | the "laravel.ui5.routes" section of the generated manifest.json.
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
    | switching to a cached registry for better performance. To do so, use
    | the `runtime` property to type hint the Ui5RuntimeInterface instance.
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
    | Dashboards are standalone UI5 artifacts that represent global entry points
    | into business workflows, such as tile-based overviews or landing pages.
    |
    | Dashboards are exposed, not embedded.
    |
    | Unlike module-bound UI5 apps, dashboards are exposed directly through the
    | Shell layer and are available independently of any specific module context.
    | They may be rendered inside the Shell container or linked to from other
    | Shell components (e.g. command palette, navigation, tiles).
    |
    | Dashboards are resolved via this configuration mapping, where the array key
    | defines the external slug used for routing and identification, and the value
    | references the Dashboard implementation class.
    |
    | The slug is part of the public URL structure (e.g. /app/dashboard/{slug})
    | and must therefore be unique across the entire application. It is considered
    | an exposure concern and must not be hard-coded inside the dashboard class.
    |
    | Each dashboard class must implement Ui5DashboardInterface and is responsible
    | for providing its metadata, permissions, and UI5 namespace, but not its final
    | route or slug.
    |
    | Example:
    | 'offers' => \Vendor\Package\Dashboards\OffersDashboard::class,
    |
    */
    'dashboards' => [
    ],

    /*
    |--------------------------------------------------------------------------
    | Registered UI5 Reports
    |--------------------------------------------------------------------------
    |
    | Reports are standalone UI5 artifacts that represent structured data outputs,
    | such as tables, lists, or analytical views, often supporting multiple output
    | formats (e.g. HTML, PDF, XLSX).
    |
    | Reports are exposed through the global Shell layer and can be accessed
    | independently of any specific UI5 application or module context. They may be
    | launched from Shell components such as dashboards, command palettes, or
    | contextual actions.
    |
    | Reports are resolved via this configuration mapping, where the array key
    | defines the external slug used for routing and identification, and the value
    | references the Report implementation class.
    |
    | The slug is part of the public URL structure (e.g. /app/report/{slug}) and
    | must therefore be unique across the entire application. It is considered an
    | exposure concern and must not be hard-coded inside the report class.
    |
    | Each report class must implement Ui5ReportInterface and is responsible for
    | defining its metadata, supported output formats, permissions, and data
    | providers, but not its final route or slug.
    |
    | Example:
    | 'timesheet' => \Vendor\Package\Reports\TimesheetReport::class,
    |
    */
    'reports' => [

    ],

    /*
    |--------------------------------------------------------------------------
    | Registered UI5 Dialogs
    |--------------------------------------------------------------------------
    |
    | This registration is only required for dialogs that should be exposed as
    | global Shell entry points; regular, app-local UI5 dialogs can be used freely
    | without being registered here.
    |
    | Global dialogs are standalone UI5 artifacts that represent modal or overlay-based
    | user interactions, such as confirmations, editors, or focused task flows.
    |
    | Dialogs are exposed through the global Shell layer and can be invoked
    | independently of any specific UI5 application or module context. They are
    | typically opened from Shell components (e.g. command palette, global actions)
    | or from other UI5 artifacts such as dashboards or reports.
    |
    | Dialogs are resolved via this configuration mapping, where the array key
    | defines the external slug used for routing and identification, and the value
    | references the Dialog implementation class.
    |
    | The slug is part of the public URL structure (e.g. /app/dialog/{slug}) and
    | must therefore be unique across the entire application. It is considered an
    | exposure concern and must not be hard-coded inside the dialog class.
    |
    | Each dialog class must implement Ui5DialogInterface and is responsible for
    | defining its metadata, permissions, UI behavior, and UI5 namespace, but not
    | its final route or slug.
    |
    | Example:
    | 'user-lock' => \Vendor\Package\Dialogs\UserLockDialog::class,
    |
    */
    'dialogs' => [

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
