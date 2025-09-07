---
outline: deep
---


# Ui5App

## Introduction

A `Ui5App` is a frontend artifact in LaravelUi5 that encapsulates a reusable UI5 app developed outside the Laravel application. It bridges a deployed `dist` folder with backend metadata, versioning, and registry integration. 

Each app is integrated into the Laravel app as part of a `Ui5Module`, and can later be discovered, versioned, and served through the LaravelUi5 registry.

> 📝 **Note**  
> A module can contain either a UI5 app or a UI5 library — but never both.

## Conceptual Overview

LaravelUi5 supports importing fully built UI5 apps and wrapping them as native Laravel modules. This integration includes:

* A `Ui5App` is a *first-class backend representation of a deployed UI5 application*.
* It is located inside a dedicated LaravelUi5 module (e.g. `ui5/Offers/`) that *must not mix in libraries*.
* Each app implements `Ui5AppInterface` and is registered via a module in `config/ui5.php`.
* The app metadata is extracted directly from the frontend build (e.g. `ui5.yaml`, `manifest.json`, `index.html`, `i18n.properties`).
* A dedicated `ServiceProvider` is generated to bootstrap the module and register it with Laravel providers.
* A minimal `composer.json` is created, allowing the module to be easily promoted into a standalone Composer package if needed.

## How to Generate

You can generate an `Ui5App` class using:

```bash
php artisan ui5:app Offers --create --vendor="Pragmatiqu IT GmbH"
```

This assumes a source folder exists in one of the following forms:

```plaintext
../ui5-offers/                ← LaravelUi5 naming convention
../io.pragmatiqu.offers/      ← SAP Easy UI5 convention
```

Before running the command, make sure to build the app via:

```bash
npm install && npm run build
```

## Options

| Option              | Description                                                   |
|:--------------------|:--------------------------------------------------------------|
| `--create`          | Scaffold new app module (fails if it already exists)          |
| `--refresh`         | Update an existing app module (fails if it does not exist)    |
| `--vendor=`         | The vendor name of the module (default: `Pragmatiqu IT GmbH`) |
| `--php-ns-prefix=`  | PHP namespace prefix (default: `Pragmatiqu`)                  |
| `--js-ns-prefix=`   | JS namespace prefix (default: `io.pragmatiqu`)                |
| `--package-prefix=` | Composer package prefix (default: `pragmatiqu`)               |

> ⚠️ When neither `--create` nor `--refresh` is provided, LaravelUi5 will decide based on the presence of the module.

## Output

Upon successful execution, the following artifacts are generated inside the Laravel project:

```plaintext
ui5/Offers/
├── composer.json               ← Composer package file
├── src/
│   ├── OffersApp.php           ← App metadata class
│   ├── OffersModule.php        ← Module wrapper
|   └── OffersServiceProvider   ← ServiceProvider class
└── resources/ui5/              ← UI5 assets (preloads, messages, etc.)
```

The build assets are copied from the `dist`-folder.

## Metadata Resolution

LaravelUi5 reads app metadata from the following locations in the source folder:

| Source file                 | Extracted data                                      |
|:----------------------------|:----------------------------------------------------|
| `ui5.yaml`                  | JS namespace, UI5 framework version                 |
| `package.json`              | App version                                         |
| `dist/manifest.json`        | `sap.ui5` section (as JSON)                         |
| `dist/index.html`           | Bootstrap data-sap-ui-\* attributes, inline JS/CSS  |
| `dist/i18n/i18n.properties` | `appTitle`, `appDescription`                        |
| `dist/i18n/*.properties`    | All files copied into Laravel `resources/ui5/i18n/` |


## Class Structure

### `OffersApp.php`

Implements:

* `Ui5AppInterface`

Provides:

* `getTitle()`, `getDescription()`, `getVersion()`, `getUi5Namespace()`.
* `getBootstrapAttributes()`, `getResourceNamespaces()`.
* `getManifestConfig()`, `getInlineScript()`, `getInlineCss()`.
* `getVendor()`.

### `OffersModule.php`

Implements:

* `Ui5ModuleInterface`

Responsibilities:

* Return the app instance via `getApp()`.
* Mark itself as root artifact via `getArtifactRoot()`.

### `OffersServiceProvider.php`

* Possibility to register classes and services in relation to the module.

## Module Integration

Each app is part of a domain-specific module. The module must be registered in your application’s `config/ui5.php` file:

```php
// config/ui5.php
return [
    'offers' => \Pragmatiqu\Offers\OffersModule::class,
];
```

This mapping defines how the app is exposed at runtime, and allows LaravelUi5 to route and manage resources per domain.

## Best Practices

* Use *CamelCase* names for apps (e.g. `Offers`, `BudgetPlanner`).
* Always run `npm run build` before executing `ui5:app`.
* Provide meaningful values for `appTitle` and `appDescription` in `i18n.properties`.
* Register your app module in `config/ui5.php` right after generation.
* Always specify `--vendor`.
* Avoid mixing libraries and apps in the same module.

## Related Links

* [Modules](./module)
* [Artifacts Overview](./index#artifact-hierarchy)
