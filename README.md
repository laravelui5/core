# LaravelUi5 Core

[![Latest release](https://img.shields.io/github/v/release/laravelui5/core?sort=semver&label=release)](https://github.com/laravelui5/core/releases/latest)
[![License: BSL 1.1](https://img.shields.io/badge/license-BSL%201.1-blue)](./LICENSE)

Foundation layer for integrating [OpenUI5](https://openui5.org) into [Laravel](https://laravel.com). Provides runtime, routing, OData integration, and developer tooling for building enterprise UI5 apps in a Laravel-native workflow.

> **What this repository is.** The public home for LaravelUi5 Core's **issues, changelog, and releases**. The package itself is a Composer dependency distributed via the private registry at `packages.pragmatiqu.io`; when you `composer require laravelui5/core`, the BSL-licensed source installs into your project's `vendor/`. This repo intentionally hosts **no source** — use it to track what changed, read release notes, and report bugs. Documentation lives at [laravelui5.com](https://laravelui5.com).

## Features

- **Artifact system** — Applications, Libraries, Cards, Dashboards, Tiles, Charts, Reports, Actions, and Resources as first-class, typed Laravel components
- **Composable dashboards** — a `composer require`'d module can contribute its Tiles and Cards into *another* module's dashboard without editing it: compose across packages, not just within one
- **OData v4** — Built on [laravelui5/odata](https://github.com/laravelui5/odata) (MIT); every UI5 app is an OData service out of the box
- **Scaffold generators** — Artisan `ui5:*` commands to generate every artifact type, plus `ui5:assemble` for a self-contained launchpad app
- **Contract-first design** — Clean interfaces and abstract bases for every artifact type; *attributes declare, classes do*

## Requirements

- PHP `^8.3`
- Laravel 13

## Installation

Core is distributed via the private Satis registry at `packages.pragmatiqu.io` and requires a free LaravelUi5 account from [laravelui5.com](https://laravelui5.com/course/#register).

Add the registry to your `composer.json`:

```json
{
    "repositories": [
        { "type": "composer", "url": "https://packages.pragmatiqu.io" }
    ]
}
```

Bind your account credentials and install:

```bash
composer config http-basic.packages.pragmatiqu.io your-email@example.com your-account-key
composer require laravelui5/core
```

`laravelui5/odata` is pulled in automatically, and the service provider is registered via Laravel's package auto-discovery — no manual wiring. See the [full installation guide](https://laravelui5.com/core/installation) for configuration, OData integration, and verification.

## Changelog

- [`CHANGELOG.md`](./CHANGELOG.md) — the running ledger of every released version
- Release announcements are published at [laravelui5.com](https://laravelui5.com)

## Documentation

- [laravelui5.com/core](https://laravelui5.com/core/) — full documentation
- [laravelui5.com](https://laravelui5.com) — guides and the video course

## SDK

For teams who want the full developer experience — prebuilt business modules, UI patterns, a DB-backed registry, and time-bound RBAC — a separate commercial **SDK** builds on Core. See [laravelui5/sdk](https://laravelui5.com/sdk/).

## Issues

Use the [issue tracker](https://github.com/laravelui5/core/issues) to report bugs and request features. (Source patches aren't accepted here — the source is private — but well-described issues and reproductions are very welcome.)

## License

[Business Source License 1.1](./LICENSE) — production use for building applications and services is permitted; repackaging as a competing framework, library, SDK, or development toolkit is prohibited. Each release converts to Apache 2.0 four years after it ships.

Licensed Work © 2026 DMG Beteiligungs GmbH — originally developed by Michael Gerzabek.
