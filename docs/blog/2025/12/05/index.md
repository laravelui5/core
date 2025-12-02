---
author: mgerzabek
title: Deep Dive
teaser: How LaravelUi5 Brings Context-Sensitive In-App Help to Enterprise Frontends
---
Below is the **full, polished, publication-ready deep-dive article draft** for **laravelui5.com** — including the requested **LeanShell clarification** woven in naturally and professionally.

The tone is **developer-friendly**, **architect-aware**, and **practical**, with **zero storytelling fluff**.
This is exactly the type of post that performs well for SaaS teams, Laravel developers, and tech leads.

---

# **Deep Dive: How LaravelUi5 Brings Context-Sensitive In-App Help to Enterprise Frontends**

*(Draft for laravelui5.com)*

---

## **Introduction**

Enterprise applications struggle with a persistent problem:
Users rarely understand a feature in the exact moment they need to use it.

Documentation lives in Confluence, Notion, Google Docs, Zendesk.
Help articles fall out of sync with releases.
And most systems don’t know where the user is or what they’re trying to do.

LaravelUi5 solves this by adding a modern, context-aware help layer *directly inside the application*.
This deep dive explains how it works.

### **Important clarification**

> **The Help System is not part of LaravelUi5/Core.**
> It is part of the **LeanShell runtime**, which is included in the commercial **LaravelUi5 SDK**.
> LeanShell uses the metadata, artifact registry, and manifest layer provided by the Core to offer the type of UX every SaaS solution for SMEs needs.

With that out of the way, let’s look at the engineering behind the help layer itself — because it demonstrates exactly what LaravelUi5/Core enables.

---

# **1. Why LaravelUi5 Needed an Integrated Help Layer**

Enterprise apps are modular. Each module ships features. Each feature changes over time.

Traditional documentation workflows break down because:

* Docs live outside the codebase
* Users must switch apps to find help
* Documentation rarely matches the release version
* Developers forget to update guides
* Onboarding becomes manual and repetitive

What we really want is:

* help that ships with the module
* help that is versioned
* help that follows the application context
* help that works with F1
* help that never drifts out of sync
* help that feels like part of the product

LaravelUi5 implements exactly that.

---

# **2. Help Lives Inside the Module**

Documentation is stored in the module itself:

```
ui5/Timesheet/
  src/
  resources/
  doc/
    123e4567-e89b-12d3-a456-426614174000/
       en.md
       de.md
       diagram.png
       diagram.de.png
    7bb34d6d-099e-4093-9284-1f09b0392a77/
       en.md
       ...
```

A few key principles:

### **One File = One Concept**

Each Markdown file contains a single topic.

### **UUID = Global Identifier**

Every help page has a unique UUID inside its frontmatter:

```yaml
id: 123e4567-e89b-12d3-a456-426614174000
title: Timesheet Overview
tags: [timesheet, overview]
```

### **Locales Live Next to the Document**

If a module provides `en.md` and `de.md`, the system serves the matching locale.

### **Assets Belong to the Topic**

Place images next to the Markdown so they remain stable and versioned.

---

# **3. Mapping Modules and Features to Help Topics**

LaravelUi5 modules can define a **help root page**:

```php
#[Help('123e4567-e89b-12d3-a456-426614174000')]
class TimesheetModule implements Ui5ModuleInterface {}
```

This becomes the module’s table of contents page — the home document.

Each artifact (action, controller, list, detail page, etc.) can optionally bind its own context:

```php
#[Context('Overview', '123e4567-e89b-12d3-a456-426614174000')]
class TimesheetList implements Ui5ArtifactInterface {}
```

This mapping ties the documentation directly to the features.

### **Runtime context from the UI5 App**

Your UI5 controller can override context dynamically:

```js
LaravelUi5.setContext("io.pragmatiqu.timesheet.Overview");
```

This makes F1 context-aware during navigation, tab changes, detail transitions, etc.

The combination of **metadata + runtime context** is what powers a true in-app help system.

---

# **4. The HelpRegistry: Scanning, Validating, and Building Metadata**

The HelpRegistry (part of the SDK) scans all modules during the `ui5:index` command.

### **It performs five tasks:**

1. **Scans `doc/*` inside every module**
   and identifies files by their UUID.

2. **Parses frontmatter**
   (title, description, tags).

3. **Detects locales**
   e.g., finds `en.md`, `de.md`.

4. **Builds maps:**

    * `documents`
    * `roots` (module root pages)
    * `bindings` (context → uuid)

5. **Generates warnings for orphaned or missing topics**

### **Example cache output**

```php
return [
  'documents' => [
    '123e4567...' => [
      'title' => 'Timesheet Overview',
      'locales' => ['en', 'de'],
      'module' => 'timesheet',
      'html' => [
         'en' => '<h1>Overview</h1>...',
         'de' => '<h1>Übersicht</h1>...',
      ],
    ],
  ],
  'roots' => [
    'timesheet' => '123e4567...',
  ],
  'bindings' => [
    'io.pragmatiqu.timesheet.Overview' => '123e4567...',
  ],
];
```

This cache is loaded by the Shell at runtime — fast, predictable, stable.

---

# **5. Markdown → HTML Conversion (Build-Time, Not Runtime)**

During `php artisan ui5:index`, all Markdown is converted:

* using unified/CommonMark or similar
* asset paths rewritten
* HTML stored in cache
* nothing left to parse on the frontend

### **Why build-time?**

* consistent rendering
* fast frontend
* stable preview
* no runtime surprises
* predictable HTML for search indexing

This is one of the biggest architectural wins of the system.

---

# **6. Four Clean Endpoints Power the Entire Help Layer**

The Help system exposes only four endpoints:

```
GET /ui5/help/{uuid}/{locale?}       → HTML + metadata
GET /ui5/help/toc/{locale?}          → module roots
GET /ui5/help/index.json             → full-text search index
GET /ui5/help/{uuid}/assets/{file}   → images, diagrams, etc.
```

### **Why so few?**

Because everything else happens:

* in the HelpRegistry (preparation)
* in the HelpService (resolution)
* in the Help UI (presentation)

The endpoints remain minimal.

---

# **7. Context Resolution — The “F1 Moment”**

The most powerful feature of this system is **context resolution**.

### When the user presses F1:

1. The UI5 app reports the current context
   (`LaravelUi5.getContext()` internally)

2. The Shell checks if a matching help UUID exists
   via `help.bindings`

3. If found → open that help page

4. If not → open the module root page

5. If even that is missing → open the global help page

6. Deep linking via `/ui5/help/{uuid}` still works

7. Back/forward navigation is supported via `pushState`

### **This is not UI logic. This is architecture.**

It ensures:

* every feature can have documentation
* every user always lands on the right page
* context is runtime-driven
* modules remain isolated
* the Shell coordinates everything

This is exactly what enterprise apps need.

---

# **8. The Help Viewer: A Mini Application Inside Your Application**

The Help UI consists of:

### **1. `<help>` Overlay**

* keyboard handling (F1, ESC)
* opens and closes viewer
* houses search overlay
* manages browser history

### **2. `<help-viewer>`**

* loads HTML from backend
* intercepts `<a>` links
* supports anchors
* manages scroll
* displays warnings (missing locale, missing document)

### **3. `<help-search>`**

* loads `/ui5/help/index.json`
* indexes documents with MiniSearch/Lunr
* shows instant results
* clicking a result opens the right page

### **Deep-linking**

Opening a help topic directly is supported:

```
/ui5/help/123e4567-e89b-12d3-a456-426614174000/en
```

This is powerful for:

* Customer support
* Internal documentation
* Developer tooling
* External knowledge bases pointing into the UI

---

# **9. Full-Text Search Built for Large Enterprise Apps**

The search index contains:

* UUID
* Title
* Description
* Tags
* Normalized HTML content (no tags)

The index is rebuilt during `ui5:index` and contains:

* one entry per document
* per locale
* fully normalized text
* extremely fast local search

No backend queries.
No external services.
Just instant results.

---

# **10. Adding Help to a Module Takes Two Minutes**

A simple workflow:

```
1. Add doc/<uuid>/en.md
2. Add #[Help(uuid)] to the module (root help page)
3. Add #[Context(..., uuid)] to artifacts (optional)
4. Run php artisan ui5:index
5. Press F1 inside the app
6. Help works — context-sensitive & versioned
```

This is simple enough that teams will actually use it — and consistent enough that documentation never drifts.

---

# **11. Summary & Next Steps**

LaravelUi5’s Help Layer:

* is part of LeanShell (not Core)
* sits on top of Core’s powerful metadata + registry
* ships help with modules
* is version-aware
* provides deep linking
* supports i18n
* handles context dynamically
* integrates with F1
* uses build-time HTML
* and gives enterprise apps a true in-product help system

This post focused on the implementation.
If you want to understand the *architecture and philosophy behind it*, read the vision article here:

**→ Help Systems Are a Meta Layer — Not an Afterthought**
*(link to pragmatiqu.io)*

And if you want to follow development, updates, and pre-release access:

**→ Join the LaravelUi5 newsletter here.**

---

# **Your deep dive is ready.**

If you want, I can now:

* Write the **pragmatiqu.io vision article** in full
* Produce **diagrams** for both posts
* Craft **landingpage content** for laravelui5.com
* Or write a **LinkedIn promo post** for both articles

What should we do next?
