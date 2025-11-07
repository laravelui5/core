---
outline: deep
---

# Ui5Registry — Central Coordination and Introspection Service

> **Since v1.1.0**
> The `Ui5Registry` provides full introspection support (Roles, Abilities, Settings, SemanticObjects)
> and hybrid semantic link discovery between Eloquent models.

The `Ui5Registry` is the central coordination and introspection service of the LaravelUi5 ecosystem.
It provides a unified API to discover, inspect, and resolve all UI5-related modules, artifacts, and metadata within a Laravel application.

It serves as the **semantic backbone** of the system, connecting static configuration, runtime reflection, and UI5 resource resolution into a single coherent model.

## Responsibilities

* Configuration-based declaration of UI5 modules and their artifacts
* Metadata introspection for Roles, Abilities, Settings, and SemanticObjects
* Semantic link discovery between models (explicit and inferred)
* Fast runtime resolution of modules and artifacts
* Central service for manifest generation, routing, and navigation

## System Rules

* Every module must have a **unique slug**
* Every artifact must have a **globally unique namespace**
* Artifacts are only accessible via their registered module or full namespace
* Semantic links may only target models that are registered as SemanticObjects

## Layer Overview

The `Ui5Registry` operates in three logical layers.

| Layer                   | Description                                                                 | Example Methods                                     |
|:------------------------|:----------------------------------------------------------------------------|:----------------------------------------------------|
| **Lookup Layer**        | Provides lookup and resolution of modules and artifacts                     | `getModule()`, `get()`, `all()`                     |
| **Introspection Layer** | Reflects and collects metadata from PHP attributes                          | `roles()`, `abilities()`, `settings()`, `objects()` |
| **Runtime Layer**       | Provides runtime path and intent resolution for UI5 and manifest generation | `slugFor()`, `resolve()`, `resolveIntents()`        |

### Lookup Layer

Handles registration and retrieval of modules and artifacts at runtime.
Modules are indexed by slug, artifacts by namespace.

Example usage:

```php
$module = $registry->getModule('users');
$artifact = $registry->get('com.laravelui5.users');
```

### Introspection Layer

The introspection layer discovers metadata via PHP attributes declared on modules and artifacts.
It collects the following categories.

| Category             | Attribute           | Description                                                 |
|:---------------------|:--------------------|:------------------------------------------------------------|
| **Roles**            | `#[Role]`           | Declares roles within a module                              |
| **Abilities**        | `#[Ability]`        | Declares abilities on backend actions                       |
| **Settings**         | `#[Setting]`        | Declares configurable module settings                       |
| **Semantic Objects** | `#[SemanticObject]` | Declares the semantic model and available navigation routes |

Since v1.1.0, Abilities and Settings are discovered dynamically for every artifact registered within a module.
The data is cached internally for fast lookup and later used in manifest.json generation.

### Runtime Layer

The runtime layer is responsible for path resolution, intent discovery, and runtime linking of UI5 resources.
It bridges the reflection-based metadata with actual runtime routes and assets.

**Responsibilities**

* Resolving artifacts and resource paths by slug
* Mapping namespaces to modules
* Exposing semantic navigation routes for manifest.json
* Generating UI5 resource roots for bootstrap configuration
* Providing reverse-intent resolution between linked SemanticObjects

## Semantic Links

*Starting with v1.1.0*, the Registry supports **hybrid semantic link discovery**.
Links between SemanticObjects can be declared explicitly via
`#[SemanticLink(model: Target::class)]`
or inferred automatically from Eloquent relations (`belongsTo`, `hasOne`).

This hybrid approach keeps the system ORM-agnostic yet fully aware of semantic relationships,
allowing cross-module navigation without database constraints.

Example:

```php
#[SemanticLink]
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
```

## Semantic Intents

The `resolveIntents()` method provides navigation intents based on registered SemanticObjects
and discovered SemanticLinks. It reverses the direction of declared links and returns
all routes that *point to* the given module.

Example:

```php
$intents = $registry->resolveIntents('users');
```

Output:

```php
[
  "Order" => [
    "details" => [
      "label" => "Order Details",
      "icon"  => null
    ]
  ]
]
```

This result indicates that **Orders** link to **Users**,
and exposes their navigable intents (e.g., “Order Details” in the UI).

## Resource Resolution

The registry also resolves resource roots and versioned UI5 paths for module assets.

```php
$path = $registry->resolve('com.laravelui5.users');
// => /ui5/app/users/1.0.0

$roots = $registry->resolveRoots(['com.laravelui5.users', 'com.laravelui5.offers']);
/*
[
  "com.laravelui5.users"  => "/ui5/app/users/1.0.0",
  "com.laravelui5.offers" => "/ui5/app/offers/1.0.0"
]
*/
```

## Introspection Summary

The `introspect()` method returns a snapshot of the registry’s internal state:

```php
$data = $registry->introspect();
```

Structure:

* `modules`
* `artifacts`
* `namespaceToModule`
* `slugs`
* `roles`
* `abilities`
* `objects`
* `links`

## Final Notes

Since v1.1.0, the `Ui5Registry` forms the introspective backbone of the LaravelUi5 Core,
bridging static metadata, runtime reflection, and UI5 integration
through a unified semantic graph.
