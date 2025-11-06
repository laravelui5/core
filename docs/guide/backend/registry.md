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

---
---

## Understanding the Ui5Registry Layers

The **Ui5Registry** is the central lookup and introspection service  
in the LaravelUi5 Core. It represents the *semantic heart* of the entire system —  
bridging PHP metadata, Laravel runtime, and UI5 manifest output.

To keep things maintainable and predictable, the Registry operates across **three distinct layers** that are summarized in the following sections.

### Lookup Layer

> “What exists, and how can I find it?”

This layer provides **fast, structural access** to all registered modules and artifacts.  
It doesn’t use Reflection — it simply exposes already-known objects (from cache or boot discovery).

#### Responsibilities
- Fast lookup of modules and artifacts  
- Routing-safe and rendering-safe resolution  
- Base for all higher-level layers

#### Typical Methods
```php
$module = $registry->getModule('users');
$artifact = $registry->get('io.pragmatiqu.users.cards.summary');
$allModules = $registry->modules();
````

| Method                | Purpose                    |
|:----------------------|:---------------------------|
| `get()`               | Find artifact by namespace |
| `getModule()`         | Find module by slug        |
| `has()`               | Check if artifact exists   |
| `fromSlug()`          | Reverse-lookup by slug     |
| `slugFor()`           | Get full route slug        |
| `all()` / `modules()` | List everything            |

### Introspection Layer

> “Which metadata is defined through PHP Attributes?”

This layer performs **deep reflection** over all LaravelUi5 modules,
collecting metadata such as roles, abilities, semantic objects, and settings.
It’s the **source of truth** for the SDK, manifest generation, and permission systems.

#### Responsibilities

* Parse PHP attributes (e.g. `#[Role]`, `#[Ability]`, `#[SemanticObject]`)
* Structure semantic metadata for all modules
* Provide a unified view of backend capabilities

#### Typical Methods

```php
$roles = $registry->roles();
$abilities = $registry->abilities();
$settings = $registry->settings();
$objects = $registry->objects();
```

| Method        | Returns                               | Source              |
|:--------------|:--------------------------------------|:--------------------|
| `roles()`     | Declared roles across all modules     | `#[Role]`           |
| `abilities()` | Declared abilities                    | `#[Ability]`        |
| `settings()`  | Configurable or tenant-level settings | `#[Setting]`        |
| `objects()`   | Semantic business objects and routes  | `#[SemanticObject]` |

Example structure for `objects()`:

```php
[
  "User" => [
    "name" => "User",
    "module" => "users",
    "routes" => [
      "display" => ["label" => "Show", "icon" => "sap-icon://display"],
      "edit" => ["label" => "Edit", "icon" => "sap-icon://edit"]
    ]
  ]
]
```

### Runtime Layer

> “How do these elements work together at runtime?”

This layer derives **contextual information** during runtime —
for example, navigation intents, resource roots, or semantic relations between objects.

It depends on the Lookup and Introspection data but provides runtime-ready results
for routers, manifest generators, and UI5 frontends.

#### Responsibilities

* Derive semantic intents from object graph
* Generate `resourceroots` and manifest paths
* Connect backend and frontend semantics

#### Typical Methods

```php
$roots = $registry->resolveRoots(['io.pragmatiqu.users']);
$intents = $registry->resolveIntents('users');
```

| Method                      | Description                                       |
|:----------------------------|:--------------------------------------------------|
| `resolveIntents($module)`   | Returns UI5 navigation intents for a given module |
| `resolveRoots($namespaces)` | Builds resource root map for manifest.json        |
| `artifactToModuleSlug()`    | Maps an artifact class to its module              |
| `namespaceToModuleSlug()`   | Maps a namespace to its owning module             |

### Conceptual Overview

```
                ┌──────────────────────────────────────────┐
                │              Runtime Layer               │
                │  resolveIntents() • resolveRoots()       │
                │  namespaceToModuleSlug() • artifactTo…   │
                └──────────────────────────────────────────┘
                                ▲
                                │ uses data from
                                │
                ┌──────────────────────────────────────────┐
                │       Reflection / Introspection Layer    │
                │  roles() • abilities() • settings() •     │
                │  objects() (SemanticObject)               │
                └──────────────────────────────────────────┘
                                ▲
                                │ builds on
                                │
                ┌──────────────────────────────────────────┐
                │           POPO / Lookup Layer             │
                │  get() • getModule() • fromSlug() • all() │
                │  (cached or live registry view)           │
                └──────────────────────────────────────────┘
```

### Summary

| Layer                          | Responsibility                     | Typical Consumer                 |
|:-------------------------------|:-----------------------------------|:---------------------------------|
| **Lookup (POPO)**              | Structural access, no reflection   | Core runtime, routing            |
| **Introspection (Reflection)** | Semantic metadata via attributes   | SDK, Manifest generator          |
| **Runtime (Resolution)**       | Derived navigation & resource data | Frontend, routers, UI5 manifests |

**In short:**

> The `Ui5Registry` is not just a lookup service — it’s a *semantic runtime system*
> bridging your Laravel backend and OpenUI5 frontend through a unified data model.
