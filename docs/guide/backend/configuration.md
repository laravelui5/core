---
outline: deep
---

# Configuration Extraction

The LaravelUi5 configuration file can be published via

```php
php artisan vendor:publish --tag=ui5-config
```

It defines all foundational settings for the LaravelUi5 platform, controlling UI5 versions, module routing, dashboards, reports, environment-specific behavior, and the integrated Lodata OData engine.

The following sections provide a structured breakdown of each configuration key, its purpose, and how it influences the system’s behavior.

## OpenUI5 Framework Version

**Key** `version`

Defines the default OpenUI5 version used for all UI5 apps unless an individual app overrides it.
Applied both during runtime resolution and during scaffolding of new self-contained or workspace-based applications.

Must be a valid version tag from the official UI5 CDN (e.g., `"1.120.5"`).

## UI5 App Routing

**Key** `routes`

Allows you to define Laravel route names that will be exposed to the UI5 application's `manifest.json`.
Useful for authentication routes or navigation purposes.

The entries are automatically resolved via Laravel's `route(...)` helper and injected into the `laravel.ui5.routes` section of the generated manifest.

**Rules**

- Values must be *valid Laravel route names*.
- URLs may be relative or absolute depending on routing config.
- Example provided in comments:

  ```php
  'routes' => [
      'login'   => 'user.login',
      'logout'  => 'user.logout',
      'profile' => 'user.profile',
      'home'    => 'dashboard.index',
  ]
  ```

## UI5 Registry Implementation

**Key** `registry`

**Value** `\LaravelUi5\Core\Ui5\Ui5Registry::class`

This option controls which implementation of the [Ui5RegistryInterface]{target="_self"} is used by the LaravelUi5 system.
By default, the in-memory registry is used best suited for development. 
In production, you should consider switching to a cached registry for better performance. 
To do so, use the `runtime` property to type hint the Ui5RuntimeInterface instance.

## Registered UI5 Modules

**Key** `modules`

This array maps a route-level "module" slug to its corresponding implementation of the [Ui5ModuleInterface]{target="_self"}.
Each module represents a cohesive functional unit within the application, containing either a UI5 application or a UI5 library, and optionally associated artifacts like cards, KPIs, reports, tiles, and actions.

The key is used as the first route segment in URLs (e.g., /app/users/...).
It must be unique across all modules to ensure correct routing and reverse lookup.

::: warning
This configuration is critical to the correct resolution of modules, artifact routing, and namespace disambiguation. 
Only experienced users should make changes here, as incorrect mappings will break route resolution and lead to ambiguous artifact lookups.
:::

The package includes a set of default modules. Avoid making changes to them unless you are absolutely certain about the consequences.

```php
'modules' => [
  'core' => \LaravelUi5\Core\CoreModule::class,
  'dashboard' => \LaravelUi5\Core\DashboardModule::class,
  'report' => \LaravelUi5\Core\ReportModule::class,
],
```

## Registered UI5 Dashboards

**Key** `dashboards`

Dashboards are standalone UI5 XML fragments used for tile-based overviews, often rendered in the shell container or as entry points in business flows.

They are not bound to a specific module and are resolved by global namespace.
Each dashboard must implement the [Ui5DashboardInterface]{target="_self"} and declare a unique JavaScript namespace for reverse lookup and permission control.

## Registered UI5 Reports

**Key** `reports`

Reports are standalone UI5 artifacts representing business evaluations with an optional selection mask, a rendered result view, and follow-up actions such as exports or workflow triggers.

Reports are not bound to a specific module and are resolved by global namespace. 
Each report must implement the [Ui5ReportInterface]{target="_self"} and register a unique urlKey and JavaScript namespace for reverse lookup and permission control.

## Active System

**Key** `active`

This value is controlled via the `SYSTEM=...` entry in your `.env` file and determines which configuration (e.g., middleware setup, proxy target) should be applied for the current environment. 
Valid values are `DEV`, `QS`, and `PRO`.

## System-Specific Configurations

**Key** `systems`

Depending on the active environment, different middleware definitions can be applied. 
Middleware is automatically loaded for all OData endpoints, provided your routing is configured accordingly.

```php
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
```

See the [landscape] page on the reasoning behind the systems switch.

## Lodata “Shadow” Configuration

These settings control the behavior of the Lodata OData engine within the LaravelUi5 environment. 
This acts as a shadow configuration that overrides the default `config/lodata.php` file and ensures that only intentional and compatible features are used.

For a concise documentation of each property consult the documentation at [lodata.io]{target="_blank"}


<!-- References -->
[landscape]: ../landscape.md
[lodata.io]: https://lodata.io/getting-started/configuration.html
[Ui5RegistryInterface]: /api/LaravelUi5.Core.Ui5.Contracts.Ui5RegistryInterface.html
[Ui5ModuleInterface]: /api/LaravelUi5.Core.Ui5.Contracts.Ui5ModuleInterface.html
[Ui5DashboardInterface]: /api/LaravelUi5.Core.Ui5.Contracts.Ui5DashboardInterface.html
[Ui5ReportInterface]: /api/LaravelUi5.Core.Ui5.Contracts.Ui5ReportInterface.html
