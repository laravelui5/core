---
outline: deep
---

# Ui5Tile

## Introduction

The `Ui5Tile` is a specialized UI5 artifact within your Laravel UI5 app. It represents a small, actionable dashboard element—typically used for navigation, quick summaries, or highlighting a specific business indicator.

Each tile is defined as a self-contained PHP class within a LaravelUi5 module. It provides title, description, namespace, and routing information, and is backed by an optional provider class that supplies runtime data or visual states.

Tiles are lightweight, dynamic, and ideal for grid-based dashboards or overview pages.

## Conceptual Overview

### Purpose

UI5 Tiles serve as interactive UI blocks that communicate status, KPIs, or entry points to deeper functionality. The `ui5:tile` command scaffolds everything needed to define and render such a tile, including backend metadata and an optional data source.

### Structure

* The main class implements `Ui5TileInterface`, describing the tile's metadata and route.
* The `Provider.php` class delivers runtime values (e.g., status, counter, color).
* Tiles are grouped under a parent app module and registered accordingly.

### Tile Lifecycle

1. Tile is declared inside a `Ui5Module` and exposed to the UI5 shell
2. The `Tile.php` class defines core attributes and links to its provider
3. At runtime, the provider delivers live data, such as totals or alerts
4. The tile is rendered inside a container or launchpad section

## How to Generate

```bash
php artisan ui5:tile Offers/ProjectKpi \
  --title="Project KPI" \
  --description="Displays aggregated project health indicators"
```

This will create a tile named `ProjectKpi` inside the `Offers` app module.

## Options

| Option            | Default                        | Description                       |
|:------------------|:-------------------------------|:----------------------------------|
| `name` (argument) | *(required)*                   | Format: `{AppName}/{TileName}`    |
| `--php-ns-prefix` | `Pragmatiqu`                   | PHP namespace prefix              |
| `--js-ns-prefix`  | `io.pragmatiqu`                | JS namespace for frontend routing |
| `--title`         | Tile class name                | Main title shown in the UI        |
| `--description`   | “Tile generated via ui5\:tile” | Description in metadata/UI        |

## Output

The following file structure is created for `Offers/ProjectKpi`:

```
ui5/
└── Offers/
    └── src/
        └── Tiles/
            └── ProjectKpi/
                ├── Tile.php
                └── Provider.php
```

## Artifact Roles

**`Tile.php`**

* Implements `Ui5TileInterface`
* Defines title, description, URL key, JS namespace
* References the `Provider` class

**`Provider.php`**

* Supplies live runtime data for the tile (e.g. value, state, icon)
* Can interact with services, repositories, or metrics

## Module Integration

Tiles must be explicitly registered in their parent module:

```php
public function getTiles(): array
{
    return [
        new Tiles\ProjectKpi\Tile(),
    ];
}
```

The system will then route and render the tile using its defined URL key and JS namespace.

## Best Practices

* Use meaningful `--title` and `--description` — they appear in dashboards and launchpads
* Keep `Tile` classes focused: one visual, one purpose
* Push conditional logic into the `Provider` class
* Use tile color/status/icon to communicate urgency
* Follow a consistent naming scheme like `App/TileName`

## Related Links

* [SAP UI5 – Tile Controls](https://sdk.openui5.org/entity/sap.m.GenericTile)
