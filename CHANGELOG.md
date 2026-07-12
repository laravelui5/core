# Changelog

All notable changes to LaravelUi5 Core are documented here, newest first. The
format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/); from
1.0.0 onward Core adheres to [Semantic Versioning](https://semver.org/).

## [2.0.0] - 2026-07-12 — Artifacts name their code by class

The first major since 1.0. One thing changes in how you write an artifact — a mechanical,
one-line edit per class — and in return the declaration style gets cleaner and the platform
gains the groundwork for the richer action feedback below. If you don't touch actions, cards,
resources, reports, tiles, charts, or apps directly, there is nothing to do.

### Changed — a one-line migration per artifact

- **An artifact now *names* the class behind it instead of building it.** Where an action
  handed back its handler — or a card / resource / report / tile / chart handed back its
  provider, or an app its manifest — it now returns that **class name** and lets the platform
  create it:

  ```php
  // before
  public function getHandler(): ActionHandlerInterface { return app(CreatePartner::class); }
  // after
  public function getHandler(): string { return CreatePartner::class; }
  ```

  The same shape applies to `getProvider`, `getTileProvider`, `getChartProvider`, and an app's
  `getLaravelUiManifest`. It reads truer — a declaration just declares, and the platform owns the
  wiring — and it is what lets an action be dispatched with the richer contract the new feedback
  builds on. Every generator (`ui5:action`, `ui5:card`, … `ui5:app`) already emits the new shape,
  so freshly scaffolded code is correct out of the box; existing artifacts each take the one-line
  edit above when you move to 2.0.

### Added

- **Actions can declare their form request.** An action may now name the request class that
  validates its body — or `null` when it carries none — so the validation contract is explicit at
  the declaration rather than implied by the handler's signature. Entirely optional: handlers that
  type a request parameter keep validating exactly as before.

- **Richer results from an action call — messages and automatic refresh.** When your backend
  returns business messages — each with a severity, its own text, and an optional field it points
  at — or tells the call which data it changed, the platform now acts on them for you: messages
  land in the form's message area (and on the field itself, when targeted), and the lists showing
  the changed data refresh on their own — all from the single action call you already make. It
  stays completely dormant until your backend sends these, so nothing existing changes in the
  meantime, and a ready-to-mount message popover is included for a form header.

## [1.4.0] - 2026-07-10 — Validation errors land on the right fields

A small, additive release: when a form submission is rejected, the platform can now show
the errors **on the fields themselves**.

### Added

- **Server validation, shown inline.** Hand a form's model to the action call, and any
  validation errors your backend returns are placed directly on the matching fields — each
  one turns red with its message, and the full list is available to a message summary — with
  no per-field wiring and no separate client-side validation rules to keep in sync. The rule
  is simply that a field's name matches its place in the form, including rows in a table, so a
  message about "the third row's rate" finds the third row's rate. Your Laravel validation
  stays the single source of truth; the form just reflects it. Submissions that fail for other
  reasons still surface as before, so nothing existing changes until you opt a form in.

## [1.3.0] - 2026-07-08 — Export a table to a file

A small, additive release: your app can now let people **download a table as a file**.

### Added

- **One-line table export.** Point the platform at a list — carrying whatever filters and
  search the user has applied — and it hands back the matching rows as a file, saved straight
  to the browser with no extra tab and no page navigation. The work happens on the server: it
  collects the *full* filtered set (not just the rows currently loaded on screen), builds the
  file, and confirms the user is allowed to export — so your front-end stays a single call. On
  Core alone the entry point is present but inert; it activates once you add the SDK, which
  provides the export itself. Write against it now and it lights up when the SDK is there.

## [1.2.0] - 2026-07-07 — Groundwork for intent-based navigation

A small, additive release: it lays the client-side groundwork for **intent-based
navigation** and smooths a TypeScript rough edge.

### Added

- **A navigation-intent entry point on the client facade.** Your app can now express
  a navigation as an *intent* — "open this dialog," "switch to that app" — and let the
  platform carry it out, instead of constructing the destination itself. On Core alone
  it is inert (there is nothing to service it yet); it activates once you add the SDK,
  whose shell authorizes and performs the navigation. Write against it now and it lights
  up when the SDK is present — nothing existing changes in the meantime.

### Fixed

- **Extending the platform's base component in TypeScript compiles cleanly again.**
  Apps that build on the base component — for example, to host a global dialog — now
  typecheck, with the dialog-open helper properly typed. The type previously omitted its
  UI5 component lineage, which broke strict-TypeScript builds.

## [1.1.0] - 2026-07-03 — Compose dashboards across modules

Dashboards are no longer confined to the module that defines them. A module you
`composer require` can now contribute its own Tiles and Cards into **another**
module's dashboard group — without that dashboard knowing about it or being
edited. Add a KPI from a new module onto an existing dashboard by registering a
single line in your service provider, and the dashboard picks it up
automatically. It's composition across packages, the way the rest of LaravelUi5
already works — and the first minor release on the stable line.

### Added

- **Cross-module dashboard composition.** A new collector lets any module place a
  Tile or Card into another module's dashboard group by namespace. Contributions
  render after the group's own children, in registration order; a dashboard that
  receives none behaves exactly as before, so nothing existing changes.

### Changed

- Core now runs on **`laravelui5/odata` 2.x** as well as 1.x — no change to your
  code either way.

## [1.0.6] - 2026-06-27 — Multiple apps from one package

A single Composer package can now ship more than one UI5 application. Alongside
the existing single-app conventions, Core resolves each app in a multi-app
package from its own `resources/ui5-<name>` folder, with the app's module living
in a matching sub-namespace. Single-app packages are unaffected — the new layout
is additive and opt-in.

### Added

- **Multi-app package source resolution.** Ship several apps from one package,
  each served from a dedicated `resources/ui5-<name>` directory (the name derives
  from the app's module). The convention is apps-only.

> This convention exists for the SDK and its bundled apps. For your own packages,
> we recommend the one-app-per-package layout.

## [1.0.5] - 2026-06-25 — Asset hardening + config documentation

A security fix and a documentation pass. In deployed environments Core now serves
only built bundles (`*-preload.js`), stylesheets, and i18n property files over
HTTP — raw module source and source maps are refused. The hardening is automatic
everywhere except `local` and `testing` and needs no configuration (the previous
opt-in key, which no host actually set, has been removed). The published
`config/ui5.php` is also now documented on every key.

### Security

- **Raw UI5 source is no longer served in deployed environments** — only built
  bundles, CSS, and `.properties`. The gate is driven by the application
  environment instead of a config flag, closing a gap where deployed systems
  could serve raw modules and source maps.

### Changed

- `config/ui5.php` is fully documented; the obsolete `ui5.active` key is removed.

## [1.0.4] - 2026-06-09 — Per-request manifest extensions

A new opt-in seam lets a manifest tailor its own `sap.ui5/extends/extensions`
node per request and acting user — for example, flipping view-element visibility
based on the current user's permissions. Existing manifests are untouched; the
seam runs only for manifests that opt in.

### Added

- **`Ui5ManifestExtensionInterface`** — implement it on a manifest to receive and
  rewrite its `extensions` node at request time. Only the extensions node is
  exposed; Core stays stateless about identity.

## [1.0.3] - 2026-06-09 — Cacheable registry data objects

A new contract lets custom data objects survive the registry cache losslessly.
Previously only modules and artifacts round-tripped through the cache cleanly; a
plain data object could be flattened to its class name. Implement the contract
and the cache serializes and rebuilds it structurally.

### Added

- **`CacheSerializable`** (`toCache(): array` / `static fromCache(array): static`)
  — opt-in, scalar-only structural caching for registry data objects. Additive
  and non-breaking.

### Changed

- The `ui5:app` base/leaf migration hint is now presented as a styled manual.

## [1.0.2] - 2026-06-05 — `ui5:app --refresh` preserves your code

`ui5:app --refresh` no longer overwrites hand-authored members of a generated App
class. Each app is now split into a framework-owned base and a developer-owned
leaf: a refresh rewrites only the base — re-syncing title, description, and
bootstrap details after a UI5 rebuild — while your identity, OData configuration,
access gates, and anything else you added in the leaf are never touched.

### Changed

- **`ui5:app` generates a base/leaf pair** — `{App}AppBase` (framework-owned,
  rewritten on refresh) and `{App}App` (yours, created once). `--refresh` only
  ever writes the base.

### Migration

- Running `--refresh` on an app created before this release creates the base from
  current source, leaves your existing class untouched, and prints the members to
  relocate into the base. Until you move them, your class harmlessly overrides the
  base — nothing breaks mid-migration.

## [1.0.1] - 2026-06-04 — Honest 401 for the OData layer

An expired or missing session on an OData request now returns a proper `401` with
an OData error envelope instead of a redirect to the login page. Because the OData
layer is only ever called by the UI5 data model (never browser-navigated), the old
redirect was parsed as data and surfaced as a misleading `Expected 'OData-Version'
header` error. Clients can now detect a real authentication failure and
re-authenticate gracefully. App (HTML) routes still redirect to login as before.

### Fixed

- **`EnsureODataAuthenticated` returns `401` JSON** on an unauthenticated request;
  the app/index route keeps its `302 → login`. The rule: OData → 401 JSON; app
  HTML → 302.

## [1.0.0] - 2026-06-03 — First stable release

Core 1.0.0 is the first stable release. The artifact contract surface is now
frozen under Semantic Versioning — breaking a frozen contract requires a major
release. Every public surface carries an honest stability label.

### Frozen

- The artifact APIs — Application, Library, Module, Card, Tile, Chart, Dashboard,
  Report, and Action — together with the Parameter API, OData service
  integration, infrastructure contributions, and the command/scaffolding API.

### Provisional

- The Resource API (stable in practice, not yet contract-frozen) and the
  SDK-bound artifact types — AnalyticTile, Dialog, ValueHelp, AnalyticsSet, and
  AnalyticCard — which Core carries as vocabulary only.

## Pre-1.0.0 (0.9.x)

The 0.9.x line (April–June 2026) was Core's in-house development toward the 1.0.0
contract freeze and was not published as a public release. It introduced the BSL
1.1 license and the move to the `laravelui5/odata` engine, and stabilized the
artifact, parameter, dashboard, and scaffolding surfaces that 1.0.0 froze.
