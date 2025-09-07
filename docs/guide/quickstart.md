---
outline: deep
---

# Getting Started

Welcome to **LaravelUi5** — your toolkit for building UI5 apps inside a Laravel project.

This guide will get you up and running in **under 2 minutes**, scaffolding a self-contained UI5 app with **zero external tooling** for a frictionless first experience.

## Install LaravelUi5 Core

Inside your existing Laravel project, install the LaravelUi5 Core package via Composer:

```bash
composer require laravelui5/core
```

This gives you access to the `ui5:*` artisan commands and sets up the LaravelUi5 runtime environment.

## Create UI5 App

Run the following artisan command to scaffold your first UI5 app.

```bash
php artisan ui5:sca Hello
```

This will:

* Scaffold a basic OpenUI5 app package at `ui5/Hello`
* Generate source files, metadata, and service provider
* Add a `HelloModule` class as the central entry point to the package
* Add a `HelloApp` class and minimal controller/view structure
* Require **no external UI5 CLI, npm, or build tools**

## Add Package

Manually add the following to your `composer.json` to make Laravel autoload the new app.

```json
"repositories": [
    {
        "type": "path",
        "url": "ui5/Hello"
    }
],
"autoload": {
    "psr-4": {
        "Pragmatiqu\\Hello\\": "ui5/Hello/src"
    }
}
```

## Add ServiceProviders

Open `bootstrap/providers.php` and add:

```php
LaravelUi5\Core\Ui5CoreServiceProvider::class,
Pragmatiqu\Hello\HelloServiceProvider::class
```

This boots the LaravelUi5 runtime and command infrastructure.

Then run:

```bash
composer dump-autoload
```

## Configure Middleware

In `bootstrap/app.php`, set up the necessary middleware stack:

```php
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use LaravelUi5\Core\Middleware\AuthenticateOData;
use LaravelUi5\Core\Middleware\FetchCsrfToken;
use LaravelUi5\Core\Middleware\ResolveUi5RuntimeContext;
use LaravelUi5\Core\Middleware\VerifyCsrfToken;

$middleware->alias([
    'auth.odata' => AuthenticateOData::class,
]);

$middleware->web(replace: [
    VerifyCsrfToken::class => VerifyCsrfToken::class,
]);

$middleware->appendToGroup('web', [
    FetchCsrfToken::class,
    ResolveUi5RuntimeContext::class,
]);
```

## Configure the Module

Publish configuration:

```php
php artisan vendor:publish --tag=ui5-config
```

Open `config/ui5.php` and add `HelloModule` to the `modules` array.

```php
'hello' => \Pragmatiqu\Hello\HelloModule::class
```

## Open in Browser

Start your Laravel server (if not already running):

```bash
php artisan serve
```

Now visit:

```
http://localhost:8000/ui5/app/hello/1.0.0/index.html
```

You should see your first UI5 app running. No extra tooling, no configs, just Laravel and UI5 working together.

## ⚠️ Heads-up

**For production, use dedicated UI5 projects for your apps!**

The `self-contained` setup is great for quick starts and learning.
But for real-world apps, we strongly recommend switching to a workspace-based setup using UI5 CLI and build tools.

When you're ready, check out the [Development Workflow](./endpoints) section to learn how to:

* Use the official UI5  setup and build tooling based on `ui5.yaml`
* Organize your apps as modular Composer packages
* Add multiple artifact types (reports, cards, tiles)
