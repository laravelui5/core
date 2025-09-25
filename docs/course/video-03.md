
# Core Setup

Now that LaravelUi5 Core is installed, I’ll show you how to wire it into your Laravel project. We’ll register the `Ui5CoreServiceProvider`, adjust the middleware stack, publish the config file, and set up your `.env` for development. This gives you a fully UI5-ready Laravel app — explicit, modular, and under your control.

<Youtube id="ba9kLBvS7Q8" />


## Quick Reference

These are the commands and snippets from this video. For the why and the bigger picture, be sure to follow along in the video above.

**1. Service Provider**

Add the service provider to `bootstrap/providers.php`.

```php
LaravelUi5\Core\Ui5CoreServiceProvider::class
```

**2. Update Laravel Cached Package Manifest**

In the terminal execute

```bash
php artisan package:discover
```

**3. Add to the middleware section**

Open `bootstrap/app.php` and add in the middleware section

```php
$middleware->web(replace: [
    \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class => \LaravelUi5\Core\Middleware\VerifyCsrfToken::class
]);
$middleware->appendToGroup('web', [
    \LaravelUi5\Core\Middleware\FetchCsrfToken::class,
    \LaravelUi5\Core\Middleware\ResolveUi5Context::class
]);
```

**4. Publish config file**

In the terminal execute

```bash
php artisan vendor:publish --tag=ui5-config
```

**5. Add SYSTEM switch**

Open your `.env` file and add the line

```bash
SYSTEM=DEV
```

## Code

To get the exact code with all edits in this video applied, run

```bash
git checkout episode-03
```
