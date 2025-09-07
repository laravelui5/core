
# Systems Landscape

LaravelUi5 adopts a clean, configurable systems architecture designed to reflect transport chains familiar from enterprise software environments — DEV, QS, and PRO.

This approach is more than just convention. It provides the foundation for a dynamic middleware stack, secure API boundaries, and, when extended, a transport-driven deployment pipeline.

## Environment Configuration

In `config/ui5.php`, you define your system landscape via the `systems` array:

```php
'systems' => [

    'DEV' => [
        'middleware' => ['web'],
    ],

    'QS' => [
        'middleware' => ['web', 'auth.odata'],
    ],

    'PRO' => [
        'middleware' => ['web', 'auth.odata'],
    ],

],
```

The currently active system is controlled via your `.env` file:

```dotenv
SYSTEM=DEV
```

## What This Enables

* Environment-specific middleware: Easily toggle authentication or runtime behavior depending on system stage.
* Modular OData exposure: Use the same codebase across multiple contexts with isolated behavior.
* Zero route hacking: No need to adjust route groups or middleware stacks manually — it’s fully driven by environment.

In this way, LaravelUi5 aligns with the separation of environments common in enterprise delivery chains, while staying simple and developer-friendly.

## From Systems to Transports

While the `SYSTEM` config primarily manages middleware today, it also opens the door to **transport-style workflows**, inspired by SAP landscapes.

If you're planning to move data or UI artifacts between DEV, QS, and PRO stages in a repeatable way, this setup provides the groundwork.

Curious about how to evolve this into a full Transport Management System (**TMS**)? Check out our free guide at [pragmatiqu.io/tms](https://pragmatiqu.io/tms) — it outlines how to

* Safely move application data between environments.
* Track artifact versions.
* Align database state with code deployments.

### What’s Next

LaravelUi5 keeps things lightweight by default, but designed for scale.
If you're building multi-tenant SaaS apps, modular internal tooling, or version-controlled UI modules, this is where clean systems separation pays off.
