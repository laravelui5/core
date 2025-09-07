---
outline: deep
---

# Ui5Module

## Introduction

A `Ui5Module` represents the top-level container for all UI5 artifacts belonging to a specific domain, such as `Users`, `Timesheet`, or `Budget`.

Each module corresponds to *exactly one* UI5 application *or* one UI5 library.

Applications may additionally define subordinate artifacts such as cards, reports, tiles, KPIs, and backend actions.

Libraries are intentionally lightweight and must not define subordinate artifacts.

## Lifecycle & Creation

Modules are not created explicitly. They are established automatically when generating either:

* an application via

  ```bash
  php artisan ui5:app Users
  ```

* or a library via

  ```bash
  php artisan ui5:lib Core
  ```

The domain name provided in these commands (e.g. `Users`, `Core`) defines the module name and directory.

> ⚠️ A module can **only contain either an application or a library** — not both.
> This is enforced by the LaravelUi5 architecture and reflected in the interface constraints.

## Artifact Composition

Each module exposes its contents via a structured API:

| Artifact Type | Method               | Notes                                                                     |
|:--------------|:---------------------|:--------------------------------------------------------------------------|
| Application   | `getApp()`           | Returns the app if present                                                |
| Library       | `getLibrary()`       | Returns the library if present                                            |
| Cards         | `getCards()`         | Returns cards tied to the module context                                  |
| Reports       | `getReports()`       | Returns reports tied to the module conetxt                                |
| Tiles         | `getTiles()`         | Returns tiles tied to the module context                                  |
| KPIs          | `getKpis()`          | Returns KPIs tied to the module context                                   |
| Actions (API) | `getActions()`       | Returns invokable endpoints via `LaravelUi5.call()` to the module context |
| –             | `getArtifactRoot()`  | Returns either the app or library tied to the module context              |

## Registration of Subordinate Artifacts

All subordinate artifacts (cards, reports, etc.) must be *explicitly registered* inside the module class, typically in the `getCards()`, `getReports()`, etc. methods.

LaravelUi5 does *not* auto-register artifacts.

Instead, each `ui5:*` command will print a helpful message after successful generation:

> 💡 Don’t forget to register this report in your module.

This pattern ensures that:

* developers retain full control over module structure,
* artifacts are only registered if intentionally exposed,
* and the module remains deterministic and predictable at runtime.

## Module Registration

All modules must be explicitly registered in your `config/ui5.php` file under the `modules` key.

The mapping uses lowercase slugs as keys and module class references as values:

```php
'modules' => [
    'users' => \Pragmatiqu\Users\UsersModule::class,
    'timesheet' => \Acme\Ui5\MyCompanyTimesheetModule::class,
],
```

This explicit mapping allows LaravelUi5 to resolve modules reliably by their *URL segment*, decoupled from their internal namespace or class name.

> 📌 This is especially useful when integrating packages from multiple vendors —
> internal module names can remain consistent, while public segments are assigned flexibly at deployment time.

> ⚠️ LaravelUi5 does **not** perform automatic scanning or registration.
> All modules must be declared explicitly to keep the system deterministic and deployment-friendly.

## Summary

* A `Ui5Module` is the structural root for all UI5-related backend and frontend logic tied to a single domain.
* Modules are created when you generate an app or library.
* Modules contain either an application *or* a library — never both.
* Only app modules may define subordinate artifacts.
* Artifact registration is explicit and code-driven.
* Integrators control URL segments independently of internal module names.

## See Also

* [Ui5App](./app)
* [Ui5Library](./library)
* [Cards](./card), [Reports](./report), [Tiles](./tile), [KPIs](./kpi), [Actions](./action)
* [Overview → Artifact Hierarchy](./index#artifact-hierarchy)
