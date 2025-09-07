---
outline: deep
---

# UI5Card

## Introduction

The `Ui5Card` is a specialized type of UI5 artifact within your Laravel UI5 app. It represents a compact, embeddable UI element—such as a KPI, list, or analytical preview—that can be dynamically rendered on dashboards or overview pages.

Each card is defined by a PHP class that encapsulates its metadata (title, description, namespace, route key) and references a Blade-based manifest used by the UI5 runtime. Optionally, a dedicated provider class supplies the underlying data logic.

Cards are modular by design. They belong to a parent `Ui5Module`, can be rendered independently via a dedicated route, and integrate seamlessly with UI5’s integration card framework—making them ideal for building reusable, dashboard-ready components in a multi-module SaaS architecture.

## Conceptual Overview

### Purpose

A UI5 Card is a modular, embeddable UI artifact used to visualize business data in a compact and interactive format. The `ui5:card` command creates the necessary files for a backend-powered UI5 Integration Card within your LaravelUi5 app.

Each card consists of:

* A PHP class implementing `Ui5CardInterface`
* A dedicated data provider
* A `manifest.blade.php` file rendered on demand

### Lifecycle Overview

1. The card is instantiated inside a LaravelUi5 module.
2. The UI5 frontend loads the manifest via a dynamic HTTP route.
3. The manifest references a data path backed by the CardProvider.
4. UI5 renders the card (e.g. KPI, List, Table, Object) in a dashboard or launchpad.
5. Optional interaction flows (e.g. navigation, filtering) are defined in the manifest.

### Structure

* The `Card.php` class defines metadata such as title, description, URL key, and JS namespace.
* The `CardProvider.php` class exposes dynamic data to the card via controller logic or services.
* The `manifest.blade.php` file contains a customizable UI5 Integration Card manifest, rendered per request.

## How to Generate

Run the following command:

```bash
php artisan ui5:card Finance/RevenueCard \
  --title="Revenue Overview" \
  --description="Displays key revenue metrics for current quarter"
```

This will:

* Create the card `RevenueCard` under the module `Finance`
* Generate all required PHP and resource files
* Set default URL key: `card/finance/revenue`

## Options

| Option            | Default                        | Description                                               |
|:------------------|:-------------------------------|:----------------------------------------------------------|
| `name` (argument) | *(required)*                   | Format: `{AppName}/{CardName}`, e.g. `Sales/OverviewCard` |
| `--php-ns-prefix` | `Pragmatiqu`                   | PHP namespace prefix                                      |
| `--js-ns-prefix`  | `io.pragmatiqu`                | JS namespace for UI5 artifacts                            |
| `--title`         | Derived from card name         | Main title shown in the UI                                |
| `--description`   | “Displays key data for {Card}” | Used as subtitle in the manifest                          |

> Note: The `Card` suffix is stripped from the URL and view names but remains in the class name.

## Output

Given the command `php artisan ui5:card Finance/RevenueCard`, the following structure is created:

```
ui5/
└── Finance/
    ├── src/
    │   └── Cards/
    │       ├── RevenueCard.php
    │       └── RevenueCardProvider.php
    └── resources/
        └── ui5/
            └── cards/
                └── revenue/
                    └── manifest.blade.php
```

## Artifact Roles

`RevenueCard.php`

* Implements `Ui5CardInterface`
* Defines `title()`, `description()`, `urlKey()`, and JS namespace
* References the corresponding provider class

`RevenueCardProvider.php`

* Optional class for providing dynamic data
* Can interact with Eloquent, services, APIs, or caching layers

`manifest.blade.php`

* UI5 Integration Card manifest rendered with Laravel Blade
* Supports localization, logic, and custom data URLs

## Module Integration

Cards must be registered in your module class by instantiating them:

```php
public function getCards(): array
{
    return [
        new Cards\RevenueCard(),
    ];
}
```

This enables discovery and dynamic routing. Each card’s manifest is resolved via the `getSlug()` defined in the card class.

## Best Practices

* Use `{Domain}/{Card}` for clarity and consistency
* Keep `Card` suffix for class names, even if stripped in the URL
* Move heavy data logic into the `Provider` class
* Use Laravel Blade conditionals and translations inside the manifest
* Prefer standard UI5 card types (`List`, `Object`, `Analytical`, `Table`)
* Reuse UI5 models and OData services where possible

## Related Links

* [SAP UI5 Integration Cards](https://ui5.sap.com/test-resources/sap/ui/integration/demokit/cardExplorer/index.html)
* [UI5 Card Explorer](https://ui5.sap.com/test-resources/sap/ui/integration/demokit/cardExplorer/webapp/index.html)
* [Using SAP Integration Cards in Your Laravel Dashboard](https://pragmatiqu.io/archive/2024/11/12)
