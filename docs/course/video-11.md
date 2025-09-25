
# The Lock Function

In this final UI-focused video, we’ll build a full-stack roundtrip: a “Toggle Lock” action for users. You’ll see how to add a lock icon, wire up a button, scaffold a Ui5Action, and connect everything from UI to backend and back again. One click changes the database and updates the UI instantly — a clean Laravel-native integration that shows the power of the LUX stack.

<Youtube id="TBN9ibiZxy4" />

## Quick Reference

These are the commands and snippets from this video. For the why and the bigger picture, be sure to follow along in the video above.

**1. Scaffold Ui5Action**

In the terminal execute

```php
php artisan ui5:action Users/ToggleLock
```

**2. Add `locked` to users**

In the terminal execute

```bash
php artisan make:migration add_locked_to_users_table --path=ui5/Users/database/migrations
```

**3. Migrate**

In the terminal execute

```bash
php artisan migrate
```

## Code

To get the exact code with all edits in this video applied, run

```bash
git checkout episode-11
```
