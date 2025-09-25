
# Installing LaravelUi5

In this lesson, I’ll walk you through setting up a clean Laravel project and installing LaravelUi5 Core. You’ll learn why we use `composer create-project` instead of `laravel new`, how to handle forks and VCS repositories, and how to configure Composer’s `minimum-stability` setting. By the end, you’ll have LaravelUi5 installed and ready to go inside a fresh Laravel baseline.

<Youtube id="hHcJouecn1k" />

## Quick Reference

These are the commands and snippets from this video. For the why and the bigger picture, be sure to follow along in the video above.

**1. Lodata Fork**

Add this snippet to your `composer.json > repositories` property.

```json
{
    "type": "vcs",
    "url": "https://github.com/pragmatiqu/lodata"
}
```

**2. Minimum Stability**

Set `composer.json > minimum-stability` to `dev`.

**3. Composer Require**

```bash
composer require laravelui5/core
```

## Code

To get the exact code with all edits in this video applied, run

```bash
git checkout episode-02
```
