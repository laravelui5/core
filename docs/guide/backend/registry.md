---
outline: deep
---

# Ui5Registry

The `Ui5Registry` is the central runtime lookup system for resolving all UI5 modules and artifacts within the LaravelUi5 ecosystem. It powers routing, rendering, manifest generation, and backend integration for all registered UI5 entities.

LaravelUi5 uses a hybrid approach of *code-first registration*, *runtime caching*, and *database synchronization* to deliver fast and predictable artifact resolution across environments.

## Purpose

The `Ui5Registry` acts as a *read-only service locator* that:

* Resolves UI5 modules by slug (e.g., `users`, `offers`)
* Resolves artifacts (e.g., cards, dashboards, actions) by their UI5 namespace
* Supports routing, UI5 tag rendering, and metadata generation
* Enforces system-wide constraints like uniqueness and slug mapping

## Artifacts & Modules

Each UI5-related PHP class in LaravelUi5 implements either:

* `Ui5ModuleInterface`: Represents a reusable module
* `Ui5ArtifactInterface`: Represents an artifact (e.g. card, tile, dashboard, report, action)

Artifacts may optionally implement `SluggableInterface` to allow addressable routing via `getSlug()`.

## Runtime Registry

The default implementation (`Ui5Registry`) performs *live resolution* on each request:

* Modules are loaded from `config/ui5.php` under the `modules` key
* Dashboards are loaded from the `dashboards` key
* Each module may provide:

    * App
    * Library
    * Cards
    * Tiles
    * KPIs
    * Actions
    * Reports

On construction, all artifacts are instantiated and registered into lookup maps by:

* Namespace (e.g., `io.pragmatiqu.users.cards.summary`)
* Slug (e.g., `users`) and URL key (e.g., `card/users/summary`)

This is ideal for development, but less performant in production.

## Cached Registry (Production)

In production environments, use the `CachedUi5Registry`, which reads from a precompiled file:

```php
bootstrap/cache/ui5.php
```

This file is created via:

```bash
php artisan ui5:cache
```

It stores artifact and module mappings. Lazy loading is supported. Classes are resolved using Laravel's container and instantiated on demand.

To activate the cached registry, set in `config/ui5.php`:

```php
'registry' => \LaravelUi5\Core\Ui5\CachedUi5Registry::class,
```

## Syncing to the Database

To store and query artifact metadata in the database (e.g., for dashboards, runtime configuration or permissions), LaravelUi5 provides a migration-based table `artifacts`.

Once the migration is run via:

```bash
php artisan migrate
```

You can sync current artifacts using:

```bash
php artisan ui5:sync
```

This command will:

* Read from the active registry implementation
* Create or update `Artifact` Eloquent records for each namespace
* Store type, version, title, description, and `url_key`

To force updates even if a record exists:

```bash
php artisan ui5:sync --force
```

## Artisan Commands

| Command     | Description                                 |
|:------------|:--------------------------------------------|
| `ui5:cache` | Generate a performance-optimized cache file |
| `ui5:sync`  | Sync artifact metadata into the database    |

## API Overview

The `Ui5RegistryInterface` defines a robust contract for artifact resolution:

```php
public function getModule(string $slug): ?Ui5ModuleInterface;
public function get(string $namespace): ?Ui5ArtifactInterface;
public function fromSlug(string $slug): ?Ui5ArtifactInterface;
public function slugFor(Ui5ArtifactInterface $artifact): ?string;
public function resolve(string $namespace): ?string;
public function resolveRoots(array $namespaces): array;
```

## System Rules Enforced

* Every module must have a *unique slug*
* Every artifact must have a *globally unique namespace*
* All routing-safe artifacts must implement `SluggableInterface`

## Best Practices

* Use `Ui5Registry::get()` when rendering components by namespace
* Use `fromSlug()` and `slugFor()` when working with routes or URLs
* Always run `ui5:cache` before deployment
* Use `ui5:sync` to persist artifact metadata for dashboards or admin UIs

## Example Use Cases

* *Resolve module from URL*: `/ui5/app/users/overview` → `users`
* *Render dynamic tag*: `<x-ui5-element id="io.pragmatiqu.users.cards.summary" />`
* *Dispatch backend action*: Call `Ui5Registry::get('io.pragmatiqu.users.actions.toggle-lock')`
