---
outline: deep
---

# Scaffold a Self-Contained UI5 App

## Introduction

The `ui5:sca` Artisan command scaffolds a *self-contained OpenUI5 application* within your Laravel project. It generates all necessary backend (PHP) and frontend (JS/XML) components inside a dedicated directory under `ui5/{Domain}` — with proper metadata, autoloading, i18n, and Laravel service provider registration.

This command is ideal for building modular, app-centric UI5 packages that are fully encapsulated with your business logic.

> 📝 **Note**  
> The `self-contained` setup is great for quick starts and learning. But for real-world apps, we strongly recommend switching to a workspace-based setup using UI5 CLI and build tools.

## Conceptual Overview

A *self-contained UI5 app* in LaravelUi5 is:

* namespaced (PHP and JS)
* versionable and autoloadable via Composer
* fully integrated into the LaravelUi5 registry system
* equipped with a `Ui5App`, `Ui5Module`, and a Laravel `ServiceProvider`
* able to serve UI5 frontend resources directly

You can think of it as a mini-application with its own identity, registry, and lifecycle.

### How to Generate

Run the following command:

```bash
php artisan ui5:sca Users
```

You may pass additional flags to customize the package namespace, frontend namespace, metadata, and ownership.

## Options

| Option             | Default                        | Description                                        |
|:-------------------|:-------------------------------|----------------------------------------------------|
| `name` (argument)  | *(required)*                   | CamelCase app name, e.g. `Users`, `Invoices`       |
| `--package-prefix` | `pragmatiqu`                   | Composer package prefix for `composer.json`        |
| `--php-ns-prefix`  | `Pragmatiqu`                   | Root PHP namespace                                 |
| `--js-ns-prefix`   | `io.pragmatiqu`                | Root JS namespace used in `Component.js`           |
| `--title`          | same as `name`                 | Title shown in i18n and metadata                   |
| `--description`    | `Ui5App generated via ui5:sca` | Human-readable description for composer + manifest |
| `--vendor`         | `Pragmatiqu IT GmbH`           | Vendor name                                        |

## Output

Running `php artisan ui5:sca Users` creates the following structure under `ui5/Users/`:

```
ui5/
└── Users/
    ├── composer.json
    ├── src/
    │   ├── UsersApp.php
    │   ├── UsersModule.php
    │   └── UsersServiceProvider.php
    └── resources/
        └── app/
            ├── Component.js
            ├── controller/
            │   └── App.controller.js
            ├── view/
            │   └── App.view.xml
            └── i18n/
                ├── i18n.properties
                └── i18n_en.properties
```

This gives you a ready-to-run UI5 frontend and the PHP classes needed to wire it into the LaravelUi5 ecosystem.

## Artifact Details

**composer.json**

* `name`: derived from `--package-prefix` and snake\_case of app name
* `description`: as passed via `--description`
* `autoload.psr-4`: for the generated PHP namespace

**PHP Side**

* `UsersApp.php`: Implements `Ui5AppInterface`, provides metadata such as title, URL key, and JS namespace.
* `UsersModule.php`: Registers the app as a `Ui5Module` within the system.
* `UsersServiceProvider.php`: Laravel service provider for automatic app registration.
* `composer.json`: Enables Composer-based registration and modularization.

**UI5 Side**

* `Component.js`: UI5 component bootstrapper including namespace, routing, and initialization.
* `controller/App.controller.js`: Entry point controller for `App.view.xml`.
* `view/App.view.xml`: Simple `sap.m.App` layout with a header/title.
* `i18n.properties`: Localized strings including `title` and `description`.

## Module Integration

To make the app discoverable by LaravelUi5, register it as a module:

```php
// config/ui5.php
return [
    'users' => \Pragmatiqu\Users\UsersModule::class,
];
```

Also, register the `ServiceProvider` in `bootstrap/providers.php`:

```php
\Pragmatiqu\Users\UsersServiceProvider::class,
```

## Best Practices

* Keep the app name short and in CamelCase.
* Always provide a meaningful `--title` and `--description` — these are shown in UIs and manifest files.
* Group apps under a consistent vendor and JS namespace.
* Use one folder per app (`ui5/{Domain}`) to ensure modularity.
* Store UI5 assets under `resources/app` to keep frontend logic isolated.

## Related Links

* [Ui5App](./app)
* [Module Registration](../quickstart#Configure-the-Module)
* [OpenUI5 Documentation](https://sdk.openui5.org/)
