
# What is `LaravelUi5`?

`LaravelUi5` is a **singleton SDK facade**, inspired by Laravel’s [Facade] pattern.

It provides a clean, centralized API for accessing backend actions, endpoints, and shared runtime features from anywhere in your UI5 application, without passing around context or dependencies manually.

### Initialization

You activate the SDK once in your `Component.js`:

```js
import LaravelUi5 from "com/laravelui5/core/LaravelUi5";

init() {
  UIComponent.prototype.init.apply(this, arguments);
    const that = this;
    sap.ui.require(["com/laravelui5/core/LaravelUi5"], function (LaravelUi5) {
        LaravelUi5.init(that);
    });
}
```

Why not just use `LaravelUi5` at the top of this module?
Short answer: `library-preload.js` timing.

When OpenUI5 loads your self-contained app, it tries to fetch all required modules early.
But if your app depends on a custom library (like `io.pragmatiqu.core`), and that library is bundled in a `library-preload.js` file, you need to wait until it's fully registered *before* using its exports.

If you `sap.ui.define([...], function(..., LaravelUi5) {...})` too early, the preload hasn’t registered `LaravelUi5.js` yet, and you’ll get undefined — or worse, an ugly 404.

So here, we delay loading until runtime with `sap.ui.require()`.
This guarantees the preload is in place, and your module resolves cleanly.

Feels a little weird? Yep. But it works. And it’s the officially supported way to safely access modules inside a preloaded UI5 library.

**Then use it anywhere**

```js
await LaravelUi5.call("toggle-lock", { user: 123 });

const config = await LaravelUi5.get("/api/settings");

if (LaravelUi5.can("archive-project")) {
  // Show button or feature
}
```

### Why a "facade"?

In Laravel, a **facade** is a static proxy to services managed by the framework’s container.

In UI5, `LaravelUi5` plays the same role: it hides complexity behind a simple API, while internally managing component context, CSRF tokens, HTTP methods, and routing logic.

It is implemented as a singleton AMD module, evaluated once and shared across your entire app.

### Benefits

* Inspired by Laravel, familiar to PHP teams
* Globally accessible – no more `this.getOwnerComponent()`
* Fully testable – no reliance on UI state
* Centralized logic – easy to extend (e.g. `withBusy()`, `getRegistry()`, `audit()`)

[Facade]: https://laravel.com/docs/facades
