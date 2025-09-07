---
outline: deep
---

# Installation

The core package for this integration is `laravelui5/core`.

This provides the full foundation for:

* SAP OpenUI5 Security Token Handling
* Compatibility with Laravel's VerifyCsrfToken middleware
* Clean separation of UI5, OData, and Laravel APIs
* Automatic OData endpoint discovery per module
* Environment-based transport configuration

## Step I: Install the package

LaravelUi5 comes pre-integrated with [`flat3/lodata`](https://lodata.io) — a powerful, Laravel-native implementation of the OData v4.01 Producer protocol.

To enable full endpoint discovery and modular registration, LaravelUi5 relies on a dedicated fork of Lodata, provided via a GitHub repository.

Before installing the package, make sure to add this repository to your `composer.json`:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/pragmatiqu/lodata"
    }
]
```

> This tells Composer to fetch `flat3/lodata` from the custom branch that supports multi-endpoint discovery.

Now install the core package:

```bash
composer require laravelui5/core
```

That’s it.

The package will automatically pull in the patched `lodata` version and preconfigure it for use with OpenUI5 and Laravel.

## Step II: Add the Service Provider

Register the `Ui5CoreServiceProvider` in your `bootstrap/providers.php`:

```php
return [
    // ... other providers ...
    LaravelUi5\Core\Ui5CoreServiceProvider::class,
];
```

## Step III: Configure middleware

In `bootstrap/app.php`, in the middleware section, add:

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

## Step IV: Publish the configuration

Publish the UI5 configuration file:

```
php artisan vendor:publish --tag=ui5-config
```

This will create:

```
config/ui5.php
```

## Step V: Configure Transport System Configuration

The configuration file `config/ui5.php` is published with a default system definition that reflects typical transport stages:

```php
'systems' => [

    'DEV' => [
        'middleware' => [
            'web',
        ],
    ],

    'QS' => [
        'middleware' => [
            'web', 'auth.odata',
        ],
    ],

    'PRO' => [
        'middleware' => [
            'web', 'auth.odata',
        ],
    ],

],
```

To activate a system, set the `SYSTEM` environment variable in your `.env` file.

```env
SYSTEM=DEV
```

This makes it easy to switch between development, QA, and production behavior, with no route or middleware changes.

## Step VI: OData Integration (Built-in)

LaravelUi5 includes full OData v4.0 support out of the box. No manual setup required.

Once installed, your UI5 modules can expose typed, discoverable data services simply by extending `Ui5App` from `Flat3\Lodata\Endpoint`.

All service discovery, routing, and registration is handled automatically.

You’re now ready to define your first module and make Laravel speak UI5’s data language.
