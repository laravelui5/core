---
outline: deep
---

# Ui5Action

## Introduction

The `Ui5Action` is an executable backend operation exposed to your UI5 frontend. It allows UI5 apps to invoke Laravel-native logic through a clean, declarative interface—ideal for tasks like toggling flags, performing validations, triggering workflows, or modifying state.

Each action consists of a metadata class (`Action.php`) and a dedicated handler (`Handler.php`). Actions are HTTP-addressable, typed, modular, and fully integrated with the LaravelUi5 registry and routing system.

## Conceptual Overview

### Purpose

UI5 Actions provide a flexible and secure way to bridge UI interaction and backend logic. They:

* expose an intent like `ToggleLock` or `ApproveInvoice`
* support typed HTTP methods (`POST`, `PATCH`, etc.)
* define routing keys and JS namespaces
* are callable via `LaravelUi5.call(...)` from the frontend

### Structure

* `Action.php`: Defines metadata (title, description, route, namespace, HTTP method)
* `Handler.php`: Contains the actual logic to be executed
* Route is automatically resolved via the action's `urlKey()` and method

### Lifecycle

1. Action is registered in a `Ui5Module` via `getActions()`
2. UI5 calls the action by name (e.g. `ToggleLock`)
3. The system resolves the route and method
4. The `Handler` class is executed with request payload
5. A response is returned (status, data, or message)

## How to Generate

Example:

```bash
php artisan ui5:action Users/ToggleLock --method=POST
```

This will generate an action called `ToggleLock` in the `Users` module, accessible via `POST /ui5/actions/users/toggle_lock`.

## Options

| Option            | Default         | Description                                  |
|:------------------|:----------------|:---------------------------------------------|
| `name` (argument) | *(required)*    | Format: `{AppName}/{ActionName}`             |
| `--method`        | `POST`          | HTTP method (e.g. `POST`, `PATCH`, `DELETE`) |
| `--php-ns-prefix` | `Pragmatiqu`    | Root namespace for PHP                       |
| `--js-ns-prefix`  | `io.pragmatiqu` | Root namespace for JS integration            |

## Output

For `Users/ToggleLock`, the following structure is generated:

```
ui5/
└── Users/
    └── src/
        └── Actions/
            └── ToggleLock/
                ├── Action.php
                └── Handler.php
```

## Artifact Roles

`Action.php`

* Implements `Ui5ActionInterface`
* Defines title, description, `urlKey()`, HTTP method, JS namespace
* Returns reference to `Handler::class`

`Handler.php`

* Contains actual logic (e.g. updates, side effects, validation)
* Invoked when the action is called from the frontend
* Returns response as `array`, `JsonResponse`, or any Laravel response type

## Module Integration

Actions are *module-bound* and must be registered in the module class:

```php
public function getActions(): array
{
    return [
        new Actions\ToggleLock\Action(),
    ];
}
```

Once registered, the action becomes callable via:

```js
await LaravelUi5.call('ToggleLock', { id: 42 });
```

The system resolves the correct URL, method, and handler automatically.

## Best Practices

* Use descriptive action names (`ToggleLock`, `DiscardDraft`, `SyncProject`)
* Prefer `POST` or `PATCH` for mutating actions
* Keep `Handler.php` focused and testable
* Return clear messages or status objects to the UI
* Use Laravel request validation if needed

## Related Links

* 
