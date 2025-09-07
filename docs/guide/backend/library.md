---
outline: deep
---

# Ui5Library

## Introduction

A `Ui5Library` is a frontend-only artifact in LaravelUi5 that encapsulates a reusable UI5 library developed outside the Laravel application. Typical use cases include formatting utilities, reusable UI components, or shared business logic intended to be consumed by multiple UI5 apps.

Each library is integrated into the Laravel app as part of a `Ui5Module`, and can later be discovered, versioned, and served through the LaravelUi5 registry.

> 📝 **Note**  
> A module can contain either a UI5 app or a UI5 library — but never both.

## Conceptual Overview

LaravelUi5 supports importing fully built UI5 libraries and wrapping them as native Laravel modules. This integration includes:

* Direct access to all build assets (e.g., preload files, message bundles).
* Rich metadata extracted from `.library`, `package.json`, and `ui5.yaml`.
* Clean namespacing on both the backend (PHP) and frontend (JavaScript).

Unlike apps, libraries *do not* support subordinate artifacts such as cards, tiles, or reports. They serve purely as shared dependencies.

## How to Generate

Use the following Artisan command to generate a new UI5 library:

```bash
php artisan ui5:lib Charts
```

This assumes a source folder exists in one of the following forms:

```plaintext
../ui5-charts-lib/                ← LaravelUi5 naming convention
../io.pragmatiqu.charts/          ← SAP Easy UI5 convention
```

Before running the command, make sure to build the library via:

```bash
npm install && npm run build
```

### Options

| Option            | Description                                                 |
|:------------------|:------------------------------------------------------------|
| `--php-ns-prefix` | Sets the backend namespace prefix (default: `Pragmatiqu`)   |
| `--create`        | Forces scaffolding a new library module                     |
| `--refresh`       | Updates assets and class files if the module already exists |

> ⚠️ When neither `--create` nor `--refresh` is provided, LaravelUi5 will decide based on the presence of the module.

## Output

Upon successful execution, the following artifacts are generated inside the Laravel project:

```plaintext
ui5/Charts/
├── src/
│   ├── ChartsLibrary.php         ← Library metadata class
│   └── ChartsModule.php          ← Module wrapper
└── resources/ui5/                ← UI5 assets (preloads, messages, etc.)
```

The build assets are copied from the following folder:

```plaintext
../ui5-charts-lib/dist/resources/io/pragmatiqu/charts/
```

## Metadata Resolution

LaravelUi5 reads library metadata from three locations in the source folder:

### `ui5.yaml`

Used to determine the UI5 namespace:

```yaml
metadata:
  name: io.pragmatiqu.charts
```

### `.library` XML

Used to extract:

* `title`
* `description`
* `vendor` (if available)

### `package.json`

Used to extract the current version of the library:

```json
{
  "version": "1.3.2"
}
```

If any of the above values are missing, fallback placeholders are inserted and should be manually reviewed.

## Class Structure

The generated `ChartsLibrary.php` class provides the following methods:

| Method             | Description                               |
|:-------------------|:------------------------------------------|
| `getTitle()`       | Returns the library title from `.library` |
| `getDescription()` | Returns the library description           |
| `getVersion()`     | Extracted from `package.json`             |
| `getNamespace()`   | The full UI5 namespace from `ui5.yaml`    |
| `getSlug()`        | The lowercase domain name used in routing |

The library is always wrapped inside a `ChartsModule.php` module, which registers it as part of the global `Ui5Registry`.

## Module Integration

Each library is part of a domain-specific module. The module must be registered in your application’s `config/ui5.php` file:

```php
'charts' => \Pragmatiqu\Charts\ChartsModule::class,
```

This mapping defines how the library is exposed at runtime, and allows LaravelUi5 to route and manage resources per domain.

## Best Practices

* Keep each library focused on a single responsibility
* Follow semantic versioning in `package.json`
* Use consistent naming between the JS namespace, folder name, and PHP class
* Avoid bundling business logic into libraries — delegate that to apps

## Related Links

* [Modules](./module)
* [Apps](./app)
* [Artifacts Overview](./index#artifact-hierarchy)
