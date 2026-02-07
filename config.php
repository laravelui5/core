<?php

use LaravelUi5\Core\CoreModule;
use LaravelUi5\Core\DashboardModule;
use LaravelUi5\Core\Middleware\ResolveUi5Context;
use LaravelUi5\Core\ReportModule;
use LaravelUi5\Core\Services\PathBasedArtifactResolver;

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
    'version' => '1.136.11',

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
    | switching to a cached registry for better performance.
    |
    */
    'registry' => \LaravelUi5\Core\Ui5\Ui5Registry::class,

    /*
    |--------------------------------------------------------------------------
    | Registered UI5 Business Modules
    |--------------------------------------------------------------------------
    |
    | This configuration explicitly declares all UI5 business modules
    | that are part of the application.
    |
    | Each entry must reference a module class implementing
    | Ui5ModuleInterface and represents a deliberate product decision:
    | only modules listed here are considered visible, supported,
    | and user-facing.
    |
    | Business modules are loaded deterministically during application
    | bootstrap and form the functional scope of the product.
    |
    | Infrastructure modules (e.g. authentication, dashboards, reporting)
    | are NOT registered here. They are provided implicitly by the platform
    | or installed packages and are collected automatically.
    |
    | Rule of thumb:
    | Visibility is a product decision, not a technical consequence.
    |
    | Example:
    | \Vendor\Package\UsersModule::class
    |
    */
    'modules' => [],

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
    'force_auth' => env('FORCE_AUTH_4_UI5', true),

    'artifact_resolvers' => [
        PathBasedArtifactResolver::class,
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
