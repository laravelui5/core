
# Scaffolding a Self-Contained UI5 App

Here’s where it gets exciting — we’ll scaffold our very first UI5 app using `php artisan ui5:sca`. I’ll explain what gets generated, why each file matters, and how to register the module. Then we’ll start the dev server and see the OpenUI5 welcome screen live in the browser. From now on, you’ll be building real, modular UI5 apps inside Laravel.


<Youtube id="eNCBaPWodiI" />


## Quick Reference

These are the commands and snippets from this video. For the why and the bigger picture, be sure to follow along in the video above.

**1. Scaffold Module Users**

In your terminal execute

```bash
php artisan ui5:sca Users
```

**2. Add the Module to the Configuration**

Open `config/ui5.php` and under the `modules` key add 

```php
'users' => \Pragmatiqu\Users\UsersModule::class,
```

**3. Add UsersServiceProvider**

Open `bootstrap/providers.php` and add

```php
\Pragmatiqu\Users\UsersServiceProvider::class,
```

**4. Add PSR-4 Mapping**

Open `composer.json` in project root and under `psr-4` add the mapping

```php
"Pragmatiqu\\Users\\": "ui5/Users/src"
```

**5. Refresh composer autoload files**

In your terminal execute

```bash
composer du
```

**6. Start Laravel dev server**

In your terminal execute

```bash
php artisan serve
```

**7. Open in browser**

In your browser open [http://localhost:8000/ui5/app/users/1.0.0/index.html](http://localhost:8000/ui5/app/users/1.0.0/index.html){target="_blank"}

## Code

To get the exact code with all edits in this video applied, run

```bash
git checkout episode-04
```
