
# Creating the Master View

Now let’s make the app come alive. We’ll build a split-screen layout with a `sap.m.SplitApp`, bind a `List` to the OData `/Users` endpoint, and render names and emails directly from the backend. No loops, no fetch calls — just declarative XML that UI5 turns into a working user list.

<Youtube id="ELbUG_jiWq4" />

## References

* [OpenUi5 SDK](https://sdk.openui5.org/){target="_blank"}

## Quick Reference

From this episode on, the code is only provided as a tagged commit. 

These are the commands from this video. For the why and the bigger picture, be sure to follow along in the video above.

**Seed Users**

In your terminal execute

```php
php artisan tinker
```

and create the test data with

```bash
User::factory()->count(100)->create();
```

## Code

To get the exact code with all edits in this video applied, run

```bash
git checkout episode-07
```
