---
outline: deep
---

# Ui5Dashboard

## Introduction

The `Ui5Dashboard` is a high-level UI artifact within your Laravel UI5 app. It represents a modular overview page designed to embed multiple tiles, cards, KPIs, or charts — providing users with a centralized starting point for insights and navigation.

Each dashboard consists of a metadata class and a corresponding Blade template. It is registered with the LaravelUi5 module system and rendered within a UI5 shell or navigation route.

## Conceptual Overview

### Purpose

Dashboards are the top layer in your UI5 app’s visual hierarchy. They group and orchestrate content such as:

* Tiles (quick access or status)
* Cards (KPI previews, lists, charts)
* KPIs (value indicators)
* Reports (access points)

The `ui5:dashboard` command creates the necessary class and template to define a dashboard in a fully encapsulated way.

### Structure

* A PHP class implementing `Ui5DashboardInterface`
* A Blade view that defines the visual layout of the dashboard
* Optional integration with cards, tiles, and KPIs via partials, or service calls

### Lifecycle

1. Dashboard is instantiated and returned by a `Ui5Module`
2. UI5 loads the dashboard using the `getSlug()` route
3. The Blade template is rendered server-side and embedded via a `sap.ui.core.mvc.View`
4. Child elements (cards, tiles, etc.) are loaded inside the layout

## How to Generate

Run the following:

```bash
php artisan ui5:dashboard Sales/ExecutiveOverview
```

This creates a dashboard named `ExecutiveOverview` inside the `Sales` app module.

## Options

| Option            | Default         | Description                           |
|:------------------|:----------------|:--------------------------------------|
| `name` (argument) | *(required)*    | Format: `{AppName}/{DashboardName}`   |
| `--php-ns-prefix` | `Pragmatiqu`    | PHP namespace prefix                  |
| `--js-ns-prefix`  | `io.pragmatiqu` | JS namespace for frontend integration |

### 5. Output

The following structure is created for `Sales/ExecutiveOverview`:

```
ui5/
└── Sales/
    ├── src/
    │   └── Dashboards/
    │       └── ExecutiveOverview.php
    └── resources/
        └── ui5/
            └── dashboards/
                └── executive-overview.blade.php
```

## Artifact Roles

`ExecutiveOverview.php`

* Implements `Ui5DashboardInterface`
* Defines title, description, JS namespace, and URL key
* Points to the Blade template path

`executive-overview.blade.php`

* Defines the layout for the dashboard UI
* Can include grid layouts, tiles, cards, components, or custom content
* Fully server-rendered with access to Laravel services and helpers

## Integration

Dashboards are *standalone UI containers* and do not need to be registered within a `Ui5Module`. Instead, they are registered globally via the `dashboards` key in your `config/ui5.php` configuration file:

```php
return [
    'dashboards' => [
        'executive-overview' => \Pragmatiqu\Sales\Dashboards\ExecutiveOverview::class,
    ],
];
```

> The key (`executive-overview`) must match the `getSlug()` returned by the dashboard class. This determines the frontend route.

Once registered, the dashboard can be accessed under:

```
/ui5/dashboards/executive-overview
```

Dashboards are rendered in full-screen or tile-like containers and can include nested UI5 artifacts (e.g., cards, tiles, reports) via standard Blade composition.

## Best Practices

* Use dashboards to group related content from the same domain
* Follow a consistent layout structure (cards left, tiles right, KPIs top, etc.)
* Pass lightweight data from the controller; fetch heavy data via cards/providers
* Avoid business logic in the Blade view — delegate to tiles, cards, or KPIs
* Use the dashboard as the default route for app modules where applicable

## Related Links

* [UI5 Views](https://sdk.openui5.org/entity/sap.ui.core.mvc.View)
