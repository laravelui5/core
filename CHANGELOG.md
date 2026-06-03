# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## Versioning reset (2026-04)

Versioning was reset from `4.3.3` to `0.9.0` in April 2026. Two changes
triggered the reset:

1. **License change to BSL 1.1.** The package was previously distributed
   under Apache 2.0. Production use remains permitted; repackaging as a
   competing UI5 toolkit/SDK is now prohibited and the license converts to
   Apache 2.0 four years after each release.
2. **Migration from `flat3/lodata` to `laravelui5/odata`.** The OData engine
   was replaced. The new library exposes `ColumnarSchemaInterface` and
   `AbstractEntitySet` as the foundation for the next round of artifact
   types (`ValueHelp`, `AnalyticsSet`) and for replacing the existing
   `Report` infrastructure.

The combination invalidates the contract surface that the `4.x` series was
stabilizing. Several artifact contracts (`SqlQueryInterface`,
`Ui5ValueHelpInterface`, `Ui5AnalyticsSetInterface`,
`Ui5ODataReportInterface`) are still in flight — see `PLAN.md` for the
phased rollout. `0.9.x` signals "contracts moving, pin tight." `1.0.0` will
ship once the PLAN's deprecation phase completes and the artifact surface
is stable.

The pre-reset history (`1.0.0` → `4.3.4`, Sept 2025 → March 2026, under
Apache 2.0) is archived in [`.changelog.asf_2.0.md`](./.changelog.asf_2.0.md)
for reference.

## SemVer credit spent (convention)

While Core is on the `0.9.x` line, breaking contract changes ship as
patch versions rather than majors. The rationale is the same as the
Versioning reset above — `0.9.x` exists precisely *because* the artifact
contracts are still moving, and consumers at this stage are all in-house
(`laravelui5-host`, `pragmatiqu.io`, in-flight SDK work). There is no
external surface for which the "breaking change → major bump" promise
has been made and would be broken.

Entries that spend SemVer credit are marked **— SemVer credit spent** in
their version heading. They still list the breaking changes plainly in
**Changed (breaking)** so in-house consumers can react. `1.0.0` is
reserved for the moment the PLAN's deprecation phase completes and the
artifact surface stabilizes.

## [1.0.0] - 2026-06-03 — the freeze

Core 1.0.0 is the freeze moment, not a code delta — it carries **no changes over
[0.9.60]**. What changes is the *promise*. The artifact contract surface the entire
`0.9.x` line existed to stabilise is now signed, and the **"SemVer credit spent"**
convention that let breaking changes ship as patches **ends here**.

Full narrative: the 1.0.0 release notes (GitHub Releases on
[`laravelui5/core`](https://github.com/laravelui5/core) + the laravelui5.com
announcement). The per-surface acceptance dossier is
`docs/meta/specs/core-1.0-acceptance.md`.

### Stability — what 1.0 commits to

Every public surface now carries an honest stability label (acceptance spec § 2):

- **Frozen** — full SemVer; breaking the contract requires a major bump. The
  artifact APIs — **Application, Library, Module, Card, Tile, Chart, Dashboard,
  Report, Action** — plus the cross-cutting **Parameter API**, **OData Service
  Integration**, **Infrastructure Contributions**, and **Command API / scaffolding**.
  All Signed across the 2026-05-27 … 06-02 review program.
- **Provisional** — may change in a minor; labelled, not hidden. The **Resource
  API** (contract pinned, no production authoring consumer yet). The SDK-bound
  artifact types — `AnalyticTile`, `Dialog`, `ValueHelp`, `AnalyticsSet`,
  `AnalyticCard` — remain enum vocabulary only in Core.

The authoring keystone is frozen with the surface: **"attributes declare, classes
do"** — an interface *method* carries an artifact's intrinsic contract; a PHP
*attribute* (`#[Slot]`/`#[Setting]`/`#[Parameter]`) carries an extrinsic declaration
to another subsystem's catalog.

### Versioning from here

- The `0.9.x` line and its **"SemVer credit spent"** banner are closed. Breaking a
  Frozen contract is now a major version — announced, with a migration path.
- Pre-reset history (`1.0.0`–`4.3.4`, Apache 2.0, Sept 2025 – March 2026) stays
  archived in `.changelog.asf_2.0.md`. **This `1.0.0` is the first stable release of
  the BSL 1.1 line and is unrelated to the archived `1.0.0`** — the April 2026 reset
  (BSL 1.1 + the `flat3/lodata` → `laravelui5/odata` engine swap) restarted the
  number line.

## [0.9.60] - 2026-06-03 — SemVer credit spent

Dependency inversion on the per-request context — breaking a `Ui5 ↔ Runtime`
namespace cycle the 0.9.58 dissolution exposed. `Ui5ContextInterface` had landed
in `Runtime\Contracts\` (the layer that *provides* the context), but the
foundation `Ui5\` domain — its heaviest consumer (capabilities, emit, veto,
dashboard transformer) — was reaching *up* into `Runtime` to import it, while
`Runtime` reached *down* into `Ui5\Contracts` for artifact types. A bidirectional
contract dependency between two domains.

Inverted it the classic way: the **contract** moves to the foundation domain that
consumes it; the **impl** stays in the layer that provides it.

### Changed (breaking)

- **`Runtime\Contracts\Ui5ContextInterface` → `Ui5\Contracts\Ui5ContextInterface`**
  — the artifact domain now owns "the context a UI5 artifact executes in," beside
  `Ui5ArtifactInterface`/`Ui5RegistryInterface`. Implemented by both Core's
  `Runtime\Ui5CoreContext` and the SDK's `SdkContext` (consumers update their `use`
  imports). The impl `Ui5CoreContext` and the factory
  `Runtime\Contracts\Ui5ContextFactoryInterface` **stay in `Runtime\`** — they're
  the provider side; the factory now imports the contract down from `Ui5\Contracts`
  (correct direction).

### Result

- **`Ui5\` → `Runtime\` is now zero edges.** The last non-context edge — a
  *docblock-only* `{@see ParameterResolverInterface}` in `Ui5ActionInterface` —
  was fully-qualified and its `use` dropped (no behavior change; the locked
  route-resolver grandfather is untouched). `Runtime\` depends inward on `Ui5\`;
  `Ui5\` depends on nothing above it — in code *and* in namespace. The layering
  rule the docs assert is now structurally enforced, frozen correctly ahead of 1.0.


Exception-home consistency pass. `Parameters\` was the lone domain localizing its
exceptions (`Parameters\Exceptions\`, the slot-pipeline faults) while every other
subsystem — including the route-bound `ParameterResolver` grandfather — throws
from the shared top-level `Exceptions\`. The split read as inconsistent (two
`*Parameter*`-named exception groups in two places). Unified on the central home,
which is correct here precisely *because* exceptions are terminal leaf types: they
carry no behavior or collaborators, share the `Ui5Exception` base, and map to HTTP
status — a cross-cutting fault vocabulary owned by no single domain. (Contrast the
0.9.58 `Contracts\`/`Services\` dissolution: those were behavior-bearing and
*must* sit with their collaborators. The discriminator is "does it do work, or
just signal a fault?")

### Changed (breaking)

The four slot-pipeline exceptions move from `Parameters\Exceptions\` to the shared
`Exceptions\` (short class names unchanged; consumers update their `use` imports):

- `Parameters\Exceptions\FixedPositionViolationException` → `Exceptions\…`
- `Parameters\Exceptions\ParameterPipelineCycleException` → `Exceptions\…`
- `Parameters\Exceptions\UnfilledRequiredSlotException` → `Exceptions\…`
- `Parameters\Exceptions\UnknownParameterSourceException` → `Exceptions\…`

`Parameters\Exceptions\` is removed. All four already extend `Ui5Exception`.
Core-internal only — no downstream consumer imports these.

## [0.9.58] - 2026-06-03 — SemVer credit spent

The last two group-by-kind namespaces — `Contracts\` and `Services\` — are
dissolved into domain-oriented homes (package-by-feature), finishing the pass
0.9.55 started. `Contracts\` was already lying: it held three concrete classes
(`Ui5CoreContext`, `Ui5Descriptor`, `Ui5Source`) and mixed the cross-cutting
per-request context contract in with five service-implementation interfaces.
Landed now, while consumers are all in-house and an import sweep is a coordinated
find-replace — after the 1.0 freeze these namespaces are fixed without a major
bump.

**This is the largest namespace move yet** (`Ui5ContextInterface` alone is ~33
downstream import sites). Short class names are unchanged — consumers update their
`use` imports only.

### Changed (breaking)

Public FQCNs moved. `Contracts\` and `Services\` are **removed**.

- **`Services\` → `Runtime\`** — a *layer* name for the request-time execution
  machinery, replacing the domain-agnostic *mechanism* name. The five resolvers/
  factories move verbatim:
  - `Services\ExecutableInvoker`, `Services\ParameterResolver`,
    `Services\SettingResolver`, `Services\PathBasedArtifactResolver`,
    `Services\CoreContextFactory`, `Services\Ui5ODataServiceRegistry` →
    `Runtime\…`
- **The five service-implementation contracts → `Runtime\Contracts\`** (co-located
  with their impls, the user's 1:1 insight):
  - `Contracts\ExecutableInvokerInterface`, `Contracts\ParameterResolverInterface`,
    `Contracts\SettingResolverInterface`, `Contracts\Ui5ArtifactResolverInterface`,
    `Contracts\Ui5ContextFactoryInterface` → `Runtime\Contracts\…`
- **The per-request context → `Runtime\` / `Runtime\Contracts\`** — the
  `Runtime` rename is what makes this correct: "the runtime context" reads right
  where "the service context" did not. It is the central *state* of the runtime
  layer, not a service detail.
  - `Contracts\Ui5ContextInterface` → `Runtime\Contracts\Ui5ContextInterface`
    (implemented by both Core's `Ui5CoreContext` and the SDK's `SdkContext`)
  - `Contracts\Ui5CoreContext` → `Runtime\Ui5CoreContext`
- **The descriptor/source contracts → `Introspection\`** — they're the abstract
  bases that `Introspection\App\*` and `Introspection\Library\*` already extend;
  zero usage in the service layer.
  - `Contracts\Ui5Descriptor` → `Introspection\Ui5Descriptor`
  - `Contracts\Ui5Source` → `Introspection\Ui5Source`

Code-generation stubs (`resources/stubs/{chart,tile}/…`) emit the new
`Runtime\Contracts\Ui5ContextInterface` import, so generated providers track the
move.

## [0.9.57] - 2026-06-03 — SemVer credit spent

`Ui5RegistryInterface`'s URL-translation methods get clearer names and loud,
typed failures — folding receiver-side URL composition into the registry. The
considered `Ui5UrlBuilder` SRP split was **dropped**: `resolve()`/`route()` are
both load-bearing (the extraction's premise of dead methods was false), so a
builder would only relocate live code and churn the SDK's `CachedRegistry` for
an aesthetic win in the freeze window. Paired with SDK (`CachedRegistry`
implements the interface) and swept across `pragmatiqu.io` + the `laravelui5/auth`
host module.

### Added

- `Ui5RegistryInterface::resolveManifestUrl(string $namespace): string` — the
  `resolve()` base with `/manifest.json` appended, **strict** (throws
  `MissingArtifactException` on an unregistered namespace rather than inheriting
  `resolve()`'s nullable footgun, which silently produced a bare
  `/manifest.json`). Both `/manifest.json` consumers route through it:
  `AbstractManifest::buildDashboards()` (was a hand-concat), and `CardEmitter`,
  which calls it inside a `try`/`catch (MissingArtifactException)` and rethrows
  its card-specific `InvalidCardManifestUrlException` (exception translation at
  the boundary) before appending the bound-slot `?{query}`. The `/manifest.json`
  suffix now lives in exactly one place.

### Changed (breaking)

- `Ui5RegistryInterface::route()` → **`resolveIndexUrl()`** (same signature
  `(string $namespace, ?string $segment = null): string`, identical output:
  `…/index.html`, or `…/index.html#/{segment}` for a non-empty segment). The new
  name states what it builds (the App index URL) and its App-only nature.
  Downstream callers updated: `pragmatiqu.io` (`PartnerStateIntentDispenser`, the
  `License{Activated,Cancelled,Refunded,Ended}` mailables, `OnboardingComplete`
  controller) and the `laravelui5/auth` `LoginRedirectController` — which now
  calls `resolveIndexUrl('io.pragmatiqu.auth', '/')` instead of hand-building
  `resolve(…) . '/index.html#/'` (byte-identical output; the last receiver-side
  app-URL concat retired).
- `resolveIndexUrl()` throws typed domain exceptions instead of bare
  `LogicException`: `MissingArtifactException` for an unregistered namespace,
  `NonRoutableArtifactException` for a non-Application artifact.
- `Ui5RegistryInterface::pathToNamespace()` parameter renamed `$namespace` → `$path`
  (it always received a URL path segment, not a namespace; signature-compatible).

### Fixed

- `NonRoutableArtifactException` passed a string where `Ui5Exception`
  (`HttpException`) expects an `int $statusCode` first — a latent `TypeError`
  that never fired because the exception had no live throw site until now. It now
  passes `404` like its `MissingArtifactException` sibling.

## [0.9.56] - 2026-06-02 — SemVer credit spent

Dashboard-family contracts moved into the family namespace; the transformer is now
explicitly internal.

### Changed (breaking)

- `Contracts\DashboardTransformerInterface` → `Ui5\Dashboard\Contracts\DashboardTransformerInterface`
  — it was the lone family-specific interface loitering in the generic top-level
  `Contracts\`; it now lives with the Dashboard family (the family-local transformer
  rule, Dashboard API D1). Core-internal — nothing downstream implements it.
- `Ui5\Dashboard\Tile` and `Ui5\Dashboard\Payload` (the `WireElement` marker
  interfaces for tile-root and tile-content controls) → `Ui5\Dashboard\Contracts\…`.
  Downstream consumers update their `use` imports (the two Portal KPI tile providers
  were swept in-house).

### Changed

- `DashboardTransformerInterface` is marked **`@internal`** and its docblock retracts
  the previously-advertised downstream extension seam (subclass
  `DefaultDashboardTransformer`, override a `protected` build method). It is bound once
  as a `singleton` and is **not** a downstream extension point — render customisation
  belongs to the artifacts' typed DTO factories, visibility to the `VetoChain`.
  `DefaultDashboardTransformer` remains the single implementation.

## [0.9.55] - 2026-06-02 — SemVer credit spent

Final pre-1.0 structural pass: the package's last *group-by-kind* namespaces are
dissolved into domain-oriented homes (package-by-feature), so the public surface
about to be frozen reads by subsystem rather than by language mechanism. Landed
deliberately now — while Core's consumers are all in-house and an import sweep is
a coordinated find-replace — because after the 1.0 freeze these namespaces are
fixed without a major bump.

### Changed (breaking)

Public FQCNs moved (short class names unchanged — consumers update their `use`
imports):

- **`Enums\` dissolved into the owning subsystem, grouped under `Enums\`:**
  - `Enums\ArtifactType`, `Enums\HttpMethod` → `Ui5\Enums\…`
  - `Enums\ParameterType`, `Enums\ValueType`, `Enums\EditLevel`, `Enums\Scope` →
    `Parameters\Enums\…`
  - `HttpMethod` lands in `Ui5\Enums\` (the Action artifact's declared verb),
    **not** under `Http\` — placing it in the adapter would create a
    `Ui5`↔`Http` package cycle.
- **`Attributes\` dissolved next to its consumer:**
  - `Attributes\Setting` → `Parameters\Attributes\Setting` (beside `#[Slot]`)
  - `Attributes\Parameter` → `Ui5\Attributes\Parameter` — the artifact-authoring
    vocabulary it decorates (`AbstractManifest` reflects over it; the
    `DataProvider`/`ActionHandler` capabilities carry it). Lives in the `Ui5`
    domain rather than next to its `Services\ParameterResolver` consumer so the
    domain keeps **no dependency on the `Services\` layer** (the placement was the
    only `Ui5 → Services` edge); `Services` depends inward on it. Stays distinct
    from the Slot pipeline (so not `Parameters\Attributes\`).
- **Inbound HTTP adapter named:** `Controllers\…` → `Http\Controllers\…` and
  `Middleware\…` → `Http\Middleware\…` — now a peer to `Commands\` (CLI adapter)
  and `Infrastructure\` (source adapter). `IndexController`'s `__DIR__`-relative
  Blade path was adjusted for the added directory level.

### Removed

- `Enums\AggregationLevel` — dead vocabulary from the KPI/AnalyticTile surface
  that moved to the SDK; zero references across Core, SDK, host, and portal.

## [0.9.54] - 2026-06-02

Re-added the `com.laravelui5.core` UI5 library bundle assets. They are needed for 
dev bootstrapping.

## [0.9.53] - 2026-06-02

Re-added `library.js` from `ui5-core-lib`.

## [0.9.52] - 2026-06-02

Added missing imports in `Provider.stub`s for chart and tile.

## [0.9.51] - 2026-06-02

### Fixed

- Dashboard groups are now first-class at every level — folder, PHP namespace,
  and artifact identity — reflecting that they are composable units a dashboard
  *references*, not owns. A `DashboardGroup` is its own `ArtifactType`, so it now
  gets its own top-level folder like every other type:
  - **Folder / PHP namespace:** `ui5:group {App}/{Name}` and the `ui5:assemble`
    blueprint write to `src/Groups/` (namespace `…\Groups`) — beside `Tiles/`,
    `Charts/`, `Cards/`, `Dashboards/`. Previously the generator landed flat in
    `src/Dashboards/` and the blueprint nested under `src/Dashboards/Groups/`.
  - **Identity:** the blueprint groups dropped the dashboard from their UI5
    namespace — `…dashboards.sales_overview.groups.X` → top-level `…groups.X`
    (matching `ui5:group`), so the identity no longer implies a group belongs to
    one dashboard.

  Organizational only — no runtime or registry impact (groups resolve through the
  dashboard's `getGroups()` instances, not by folder). Existing generated groups
  are not moved; relocate + re-namespace them to `App\Groups\…` if you want them
  on the new convention.
- `ui5:report`'s closing guidance no longer contradicts the scaffold. It claimed
  the report `declares getRequiredSlots() = [CoreSlots::DateFrom, CoreSlots::DateTo]`
  while the generated stub returned `[]` (carrying unused `CoreSlots` /
  `BackedEnum` imports). The report follows the same bare-scaffold convention as
  every other generator — empty provider / empty slot list, the developer fills
  it in — so the *message* is corrected to match the bare report (and the two
  dead imports are removed). The guidance still names `CoreSlots::DateFrom` /
  `DateTo` and `#[Slot]` as the way to parameterise it.

## [0.9.50] - 2026-06-02

**Generator input is validated before on-disk state.** `ui5:wire User --recipe=bogus`
reported *"A User floorplan already exists"* when a `User` floorplan was already
wired — the exists guard ran first and masked the invalid recipe. The same
masking affected every generator with a catalog flag: `ui5:tile|chart|card
App/Existing --seed=bogus` reported *"… already exists"* instead of the unknown
seed. An explicitly named recipe/seed is pure input, so it's now validated
*before* the exists check: a bogus value reports *"Unknown catalog entry [bogus]…"*
regardless of what's already on disk. The interactive picker and the default
still resolve *after* the exists guard, so a bare / value-less invocation never
prompts for a recipe or seed it would then refuse to use.

### Fixed

- `ui5:wire {model} --recipe=<bogus>` surfaces the unknown-recipe error even when
  a floorplan for `{model}` already exists. New
  `WireUi5ModelCommand::resolveRecipeOrFail()`.
- `ui5:tile`, `ui5:chart`, `ui5:card` `{App}/{Existing} --seed=<bogus>` surface
  the unknown-seed error even when the target artifact already exists. New
  `BaseGenerator::resolveSeedOrFail()` + `seedNamedExplicitly()` (the `false`
  sentinel distinguishes an unknown seed from the valid bare default `null`).
- `ui5:app --refresh` preserves a hand-authored version bump again. Identity is
  const-backed (`public const string VERSION = '…';`), but `readAppVersion()`
  still scanned for a `getVersion()` method body via regex — it always found
  nothing, returned null, and `--refresh` aborted with *"Could not read the
  current version…"*. It now reads the `VERSION` constant through the tokenizer
  (new `BaseGenerator::readConstString()`, mirroring `getNamespaceFromFile()`),
  robust to typed/untyped constants and sibling constants.

In all four generators, input errors now precede state errors and the existing
artifact is left untouched.

### Changed

- The bundled `com.laravelui5.core` UI5 library is slimmed to the runtime
  essentials: `library-preload.js` (+ source map), `manifest.json`, and the
  `messagebundle*.properties` bundles. The individual compiled control files
  (`controls/*.js`, the `-dbg.js` debug variants, per-file `.js.map`, the
  unbundled `library.js`/`library-dbg.js`) are dropped — UI5 loads the whole
  library from `library-preload.js` at runtime, so they were dead weight in the
  shipped package. Unminified per-control debugging happens in `ui5-core-lib`
  itself. No runtime change: the preload carries every control (Dashboard, Chart
  with the `engine` property + render-error isolation, …).

## [0.9.49] - 2026-06-02

**Charts can't take down the dashboard.** The chart-resilience pair — an empty
option no longer crashes, and a single broken chart no longer blanks its
siblings. (Released as the `ui5-core-lib` chart re-bundle; its notes were
originally appended to the 0.9.48 section, and were split back out here.)

### Fixed

- A chart with an **empty option** no longer crashes the dashboard. `ChartCanvas`
  emitted an empty `option` as the JSON array `[]`; `echarts.setOption([])` throws
  *"Cannot read properties of undefined (reading 'get')"*, which aborted the
  render mid-list (every chart below the empty one vanished). The empty case now
  serializes as `{}`, so an unconfigured chart degrades to a blank canvas. A
  freshly scaffolded `ui5:chart` (bare `option: []`) is therefore dashboard-safe.
- A single broken chart can no longer blank the dashboard. The bundled `Chart`
  control now contains any `echarts.init` / `setOption` throw inside
  `onAfterRendering` (and the resize callback): the offending chart is logged and
  left blank, and every sibling still renders. This is the defence-in-depth pair
  to the empty-option fix — it also covers *non-empty* options that are
  well-formed JSON but semantically invalid for ECharts (unknown series `type`, a
  `map` series with no registered map, `gl` types without echarts-gl, a
  `visualMap`/`encode` referencing a missing dimension). Sourced from
  `ui5-core-lib` HEAD and re-bundled into Core's served resources (the runtime
  path; the npm tag intentionally lags).

## [0.9.48] - 2026-06-02 — SemVer credit spent

**Seed names now mirror the artifact they scaffold.** The chart and card seeds
introduced in 0.9.47 were named by visualization *type* (`funnel`, `gauge`,
`pie`, `sparkline`, `list`, `object`), so tracing a polished file in a generated
app (`PipelineFunnelChartProvider.php`) back to its source stub took guesswork.
They are renamed to the artifact-kebab convention the **tile** seeds already use
(`new-customers.stub` → `NewCustomersTileProvider.php`), so the stub ↔ generated
file mapping is the standard kebab↔Pascal transform with no lookup:

| was | now | scaffolds |
|:---|:---|:---|
| `sparkline` | `calls-per-week`    | `CallsPerWeekChartProvider` |
| `funnel`    | `pipeline-funnel`   | `PipelineFunnelChartProvider` |
| `gauge`     | `quota-attainment`  | `QuotaAttainmentChartProvider` |
| `pie`       | `revenue-mix`       | `RevenueMixChartProvider` |
| `list`      | `top-deals`         | `TopDealsCard` |
| `object`    | `account-snapshot`  | `AccountSnapshotCard` |

`revenue-trend` (chart) and `kpi` (the deliberately generic standalone card
example) are unchanged. Marked *SemVer credit spent* for the `--seed` value
rename.

### Changed (breaking)

- `ui5:chart --seed=` and `ui5:card --seed=` values renamed per the table above;
  the stub filenames and `seeds.json` catalog names match. `ui5:assemble` is
  updated in lockstep, so the `launchpad-app` blueprint is unaffected.

### Changed

- The seeded dashboard's polish defaults: each chart fills its cell
  (`height: '100%'`), the gauge/donut get roomier grid cells, generated cards
  default to a 4×4 cell (`Card.stub`), and the dashboard's root `VBox` carries a
  responsive margin (`AbstractUi5Dashboard::getVBox()`) so the grid breathes
  against the page edge.

### Fixed

- Malformed generator names report a calm one-line error instead of an uncaught
  `InvalidArgumentException` + stack trace. `php artisan ui5:tile Finance` (no
  `App/Object` separator) and `ui5:tile Finance/foo` (non-CamelCase part) now
  print the guidance and exit non-zero. Applies to every `ui5:*` generator via a
  shared `BaseGenerator::parseAppObjectOrFail()` wrapper.

## [0.9.47] - 2026-06-02

**The `launchpad-app` blueprint becomes a real showcase.** The assembled
dashboard grows from one group (3 KPI tiles + 1 chart) into three semantic
groups densely populated with varied visualizations — the density that makes a
dashboard read as "wow" rather than "scaffold". New, independently reusable
generator seeds back it:

- **4 chart seeds** (`ui5:chart … --seed=`): `sparkline` (axis-less micro-trend),
  `funnel` (pipeline by stage), `gauge` (single-value progress arc), `pie`
  (donut composition). Each is an ECharts `option` rendered through the Core
  `Chart` control — including the donut, which is a `pie` series in our own
  control, *not* SAP's analytical card type.
- **2 card seeds** (`ui5:card … --seed=`): `list` (Top Deals) and `object`
  (Account Snapshot), modelled on the pragmatiqu.io portal's Activity-feed and
  Org-profile cards. Manifest-only static data (spec D4); the scaffolded
  provider stays bare.

The three groups: **This Month** (3 KPI tiles + calls/week sparkline) ·
**Pipeline** (funnel + revenue-trend + Top Deals list) · **Performance** (quota
gauge + revenue-mix donut + Account Snapshot object).

### Added

- Chart seeds `sparkline`, `funnel`, `gauge`, `pie` (`resources/stubs/chart/seeds/`).
- Card seeds `list`, `object` (`resources/stubs/card/seeds/`).
- `launchpad-app` blueprint ships two new dashboard-group stubs (`PipelineGroup`,
  `PerformanceGroup`) beside the renamed `ThisMonthGroup`.

### Changed

- `ui5:assemble` orchestrates the full bundle: 3 tiles + 5 charts + 2 cards
  wired across the 3 groups, all pre-registered on the module and dashboard.
- `AbstractUi5DashboardGroup`'s default `getPanel()` now renders with a solid
  background (`BackgroundDesign::Solid`) and a small bottom margin, so groups
  read as distinct cards out of the box. Consumers overriding `getPanel()` are
  unaffected.

## [0.9.46] - 2026-06-02

**Minor tweak of the chart control.** The bundled `com.laravelui5.core` UI5
library carries a re-built `Chart` control: it now declares the `engine`
property that the server wire envelope (`com.laravelui5.core.Chart` / the PHP
`ChartCanvas` DTO) has always emitted alongside `option`. Without the
declaration, the dashboard tree-walker forwarded `engine` to the control
constructor and `ManagedObject.apply` threw *"encountered unknown setting
'engine' for class 'com.laravelui5.core.controls.Chart'"* in the browser — so
any dashboard containing a chart (including the `launchpad-app` blueprint) failed
to render. Surfaced by a fresh-install smoke of `ui5:assemble`.

The control still renders exclusively via the host-provided `window.echarts`
global at v0.9; `engine` (default `"echarts"`) is accepted as the forward-compat
seat for engine pluralism, not yet branched on.

### Fixed

- Bundled `Chart` control (`resources/ui5/`) declares the `engine` property, so
  dashboards with charts no longer fail with `ManagedObject.apply: unknown
  setting 'engine'`. Sourced from `ui5-core-lib` HEAD and re-bundled into Core's
  served resources (the runtime path; the npm tag intentionally lags).

### Changed

- `ui5:assemble` closing output names the three per-app wiring edits (the
  `Showcase\` autoload, the provider, the module) plus `composer dump-autoload`,
  and points charts at `vendor:publish --tag=ui5-views`. Refines the 0.9.45
  output change: the CSRF middleware swap is one-time framework setup, not a
  per-assembly edit, so it is no longer listed among the per-app steps.

## [0.9.45] - 2026-06-02 — SemVer credit spent

**Laravel 13 only, and a smoother `ui5:assemble` on-ramp.** Core drops Laravel
12 from its dependency range and standardizes on Laravel 13. The trigger was the
CSRF middleware rename: Laravel 13 introduced `PreventRequestForgery` as the
canonical request-forgery middleware and deprecated `ValidateCsrfToken`. Core's
`VerifyCsrfToken` (the SAP CSRF-handshake-aware drop-in) now extends
`PreventRequestForgery`, and the host-side `bootstrap/app.php` swap targets a
single class name instead of branching per Laravel version.

Alongside it, the self-contained assembly flow surfaced in a fresh-install smoke
test got three fixes: the generated app no longer fatals on asset resolution,
the bootstrap-extension views are publishable, and the command's output is
legible (the leaf generators ran loud, printing registration directives for
artifacts the blueprint already wires).

Marked *SemVer credit spent* for narrowing the supported Laravel range to `^13.0`.

### Changed (breaking)

- Supported Laravel range narrowed from `^12.0 || ^13.0` to `^13.0`
  (`illuminate/*`). Dev harness `orchestra/testbench` bumped `^10.6` → `^11.0`.
  Downstream hosts still on Laravel 12 (e.g. `laravelui5-host`) must bump to
  `^13.0` before updating Core.
- `LaravelUi5\Core\Middleware\VerifyCsrfToken` now extends
  `Illuminate\Foundation\Http\Middleware\PreventRequestForgery` (was
  `ValidateCsrfToken`). The host middleware swap is now
  `replace: [PreventRequestForgery::class => VerifyCsrfToken::class]`.

### Added

- `vendor:publish --tag=ui5-views` publishes starter bootstrap-extension views
  (`resources/views/ui5/head.blade.php` + `foot.blade.php`) into the host's own
  views folder. The `head` starter loads ECharts from a CDN and carries a
  commented `@includeIfSdk('ui5::head')` example. Independent of `ui5:assemble`
  (the seats are host-scoped, not per-app). Implements the publish path for the
  Core Bootstrap Mechanism v0.9 host-extension seats.

### Changed

- `ui5:assemble` now runs its leaf generators silently and prints a single
  `yo`-style listing of every file written. Previously each leaf printed its
  standalone "register it in your module's `getTiles()` / the Group's
  `getChildNamespaces()`" directives — correct for a hand-run `ui5:tile`, but
  misleading under assembly, where the blueprint's Module and Group already
  reference those namespaces. The closing output now points at the host wiring
  steps and the `ui5-views` publish.

### Fixed

- The `launchpad-app` blueprint's generated app (`ShowcaseApp`) implements
  `getAssetPath()` directly (assets live in `resources/app/`) instead of using
  the `HasAssets` trait (which resolves against `resources/ui5/`), and imports
  the `File` facade it relies on — the previous combination resolved the facade
  in the app's own namespace and fataled on the first asset request.
- The blueprint `manifest.json` declares the `sap.f` and `sap.ui.integration`
  libraries required by the dashboard tiles and cards.
- The seeded tile/chart providers set `GridContainerItemLayoutData` on their
  controls so they lay out correctly in the dashboard grid.
- The app view binds `<lux:Dashboard>` at the blueprint's templated namespace
  instead of a hardcoded string.

## [0.9.44] - 2026-06-02 — SemVer credit spent

**`ui5:slot:list` + `ui5:slot:show` merge into a single `ui5:slot`.** The slot
inspector now follows the same optional-argument reading the Command API
catalog uses (*named → detail; empty → overview*): `ui5:slot` lists the whole
catalog, `ui5:slot {name}` shows one slot's resolution chain. This also folds
the slot family back into the flat `ui5:*` naming of every other generator —
the two `ui5:slot:*` commands were the only deeper-namespaced commands in the
suite. The duplicated `formatDefault()` helper collapses into one.

Marked *SemVer credit spent* for the removal of the `ui5:slot:list` and
`ui5:slot:show` command names.

### Changed (breaking)

- `ui5:slot:list` and `ui5:slot:show` are removed; use `ui5:slot` (no argument
  lists the catalog) and `ui5:slot {name}` (shows one slot) respectively.
  Unknown-slot behaviour is unchanged — a missing name still fails with
  `Slot [x] is not in the catalog.` and a non-zero exit.

## [0.9.43] - 2026-06-01 — SemVer credit spent

**Command API — `ui5:*` generators, the seed catalog, and self-contained
assembly** (spec `core-self-contained-application-v0.9.md`). One coherent
scaffolding surface lands together: a **bare-bones baseline** every generator
obeys (emit a clean shell, never smuggle sample data), a **`--seed` enrichment**
on the three dashboard-leaf generators, the self-contained assembly command
**`ui5:assemble`**, and **`ui5:wire`** for the master/detail data act. A single
**catalog grammar** unifies the three enrichment/assembly points — *named → run
it; empty → interactive picker over a `{ name, description, … }` JSON
descriptor* — so the mechanism is learned once and reused everywhere
(seeds · blueprints · recipes). `resources/stubs/` is reorganized from a flat
folder into per-artifact groups, with two new top-level catalog siblings
(`blueprints/`, `recipes/`).

Marked *SemVer credit spent* for two deliberate breaking changes: the leaf
generators are now bare-by-default, and the legacy `ui5:sca` command is removed
in favour of `ui5:assemble`.

### Added

- **The catalog primitive** (`src/Commands/Catalog/`) — the one reusable grammar
  all three consumers read, built up front because all three are known (D8):
  - `CatalogInterface` — a loaded catalog (`all()`/`names()`/`has()`/`get()`).
  - `JsonCatalog` — backed by a bare JSON array of `{ name, description, … }`
    rows; facade-free (plain `file_get_contents`), a pure unit with no booted
    app.
  - `CatalogEntry` — a `final readonly` row VO; `name`+`description` are
    first-class, any per-domain column (e.g. a seed's `stub`) survives in
    `$data` via `get()`.
  - `CatalogPicker` — the shared resolution helper: a named entry resolves
    directly; a null name renders a Laravel Prompts `select()` picker with the
    description folded into the label.
  - `UnknownCatalogEntryException` — a named miss reports the available names
    rather than failing silently (R4).
- **`--seed=<name>` on `ui5:tile`, `ui5:chart`, `ui5:card`** (spec §3). The seed
  swaps exactly the artifact's one data-bearing file — the `TileProvider` /
  `ChartProvider` for tiles/charts, **the card's `manifest.json` only** for cards
  (static visualization data, deliberately not provider-bound — D4); the POPO and
  every other scaffolded file are untouched. Seed catalogs ship `tile/seeds/`
  (`revenue`, `new-customers`, `open-deals`), `chart/seeds/` (`revenue-trend`),
  `card/seeds/` (`kpi`). Catalogs are open content (§10): a new seed is a stub +
  a `seeds.json` row, no command change.
- **Standardized continuation output** (spec §2.4): every dashboard-leaf
  generator prints the exact registration directives — the module method
  (`getTiles()`/`getCards()`/`getCharts()`) and the owning Dashboard Group's
  `getChildNamespaces()`, each with the literal line to add.
- **`ui5:wire {model?} --recipe=<name>`** (`WireUi5ModelCommand`) — the
  master/detail data act (spec §6). Lands a `{Model}.view.xml` +
  `{Model}.controller.js` into the `Showcase` app's
  `resources/app/{view,controller}/`. Two axes mirroring `--seed` (D6): the model
  is the identity argument, the recipe is the selectable floorplan
  (`ui5:wire User` → default; `--recipe` → picker; bare `ui5:wire` → prompt model
  then recipe). Core-stateless-clean (R3): templates the model name + app
  namespace, never introspects a model; the OData reveal (`extends
  AbstractUi5App`, `discoverModel(Model::class)`, the client route) is the
  presenter's live edit, named in the continuation output (R5). Ships the
  `recipes/` catalog (D7, a top-level sibling outside `stubs/`) with the
  `master-detail` recipe.
- **`ui5:assemble {blueprint?}`** (`AssembleUi5AppCommand`) — the self-contained
  assembly command, the flagship consumer (spec §5). One fixed `Showcase` app
  (D9): no `{name}` argument, no prefix/title/vendor options; the namespace root
  + vendor are `public const` on the command (a single teaching on-ramp à la
  Breeze, not an app factory — the consumer owns the files and renames
  post-gen). Single-shot with a target-exists guard. **Leaf orchestration +
  pre-wired boot guarantee (D10):** after emitting the blueprint's static files
  it invokes `ui5:tile`/`ui5:chart` with deterministic names — dogfooding the
  `--seed` catalog — and ships a pre-wired `Module` (plus `SalesOverview`
  dashboard + `ThisMonth` group) whose registrations already reference those
  namespaces, so the app boots onto a working "Sales Overview" dashboard
  (1 dashboard + 1 group + 3 tiles + 1 chart) from one command. The pre-wired
  module is a stub *with content*, not anchor injection — explicit composition
  stays manual (§1). Ships the `blueprints/` catalog (D7) with `launchpad-app`.
- **`BaseGenerator::compileStubFrom()`** — compiles a template from an absolute
  path, so the `recipes/` and `blueprints/` catalogs load without rebasing
  through the `stubs/`-rooted `compileStub()` (which now delegates to it, no
  behavioural change).
- Explicit `laravel/prompts` dependency (already present transitively via
  `illuminate/console`; now declared because the picker uses it directly).

### Changed

- **`resources/stubs/` reorganized into per-artifact folders** (spec §4),
  applying the **D11 stub-naming convention** — the folder supplies context, so
  a class stub takes its canonical role token and a non-class stub its file-kind
  token. Every `compileStub()` call was rebased; no behavioural change to the
  reorg itself. Folders: `action/` (`Action.stub`, `Handler.stub`), `app/`,
  `card/` (incl. `manifest.stub`, `i18n.stub`), `chart/`, `dashboard/`, `group/`,
  `lib/`, `report/` (`document.stub` ← `report.blade.stub`), `resource/`,
  `tile/`. The subordinate data class is `Provider.stub` in every folder. The
  self-contained scaffolding relocated into the new
  `blueprints/launchpad-app/` (the JSON app manifest is `manifest.json.stub`, the
  PHP manifest class `Manifest.stub` — split to avoid a case-insensitive
  filesystem collision).
- **Bare-by-default — *breaking* (R6/D1).** `ui5:tile`/`ui5:chart`/`ui5:card` no
  longer emit sample data by default; they scaffold a bare, TODO-guided shell
  instead of the previous smuggled samples (`42 EUR`, the `Q1–Q4
  [120,200,150,80]` series, `Sample KPI 1234.56`). Sample data now has exactly
  one door — `--seed`. Recover the old output with `--seed=revenue` /
  `--seed=revenue-trend` / `--seed=kpi`.
- **The self-contained app stub de-drifted** (`blueprints/launchpad-app/App.stub`,
  V8): const-backed identity via `HasArtifactIdentity` + `HasAssets` (was
  hand-rolled getters); `getSource()`/`getManifestPath()` delegate to the source
  strategy (was a hardcoded `__DIR__` path); a real vendor (was "Vendor not
  supplied"); the redundant `oninit`/`{{ component }}` boot path removed (the
  head script owns component creation). It implements `Ui5AppInterface`
  **directly** (no `AbstractUi5App`) and is OData-free — the floor the
  `ui5:wire` reveal builds on.

### Removed

- **`ui5:sca` (`GenerateSelfContainedUi5AppCommand`) — *breaking*.** Replaced by
  `ui5:assemble`; the `{name}`/prefix/vendor options are gone (D9). Replace
  `ui5:sca Foo …` with `ui5:assemble launchpad-app`.

> The "boots onto a working dashboard" guarantee is verified by generator tests
> for wiring shape + namespace match; the rendered-pixels end-to-end check is a
> browser smoke against a real host (Slice 5 runbook).

## [0.9.42] - 2026-05-29 — SemVer credit spent

**Ui5 namespace reorg — the control *vocabulary* and the emit *grammar* both promoted out of `Dashboard\` into neutral, reusable layers (`Ui5\Controls\`, `Ui5\Emit\`).** Pure PHP relocation + rename; zero wire-shape change; zero behavioural change. Neither was ever dashboard-specific — the dashboard was simply their first and so-far-only consumer. As forms, tables, and richer reports grow the same server-rendered-control need, both layers now live where a non-dashboard family can build against them without importing through `Dashboard\`. Two coordinated parts, applying the 0.9.41 convention (`Sap/` = mirrors of stock UI5 wire classes, `Lux/` = our own compositions) honestly across the tree.

### Part 1 — Emit grammar → `Ui5\Emit\`

The emit machinery contracts — the universal grammar for translating an artifact tree into a `{ui5.class: props}` wire tree: the marker + emitter + context + veto contracts every composite-control family needs.

**The decision that made this safe to do now (rather than waiting for the 2nd consumer):** the *transformer* is **not** part of the neutral layer. Its input is the root artifact type (`transform(Ui5DashboardInterface, …)`), so it is inherently family-local — each family ships its own typed transformer producing the neutral emitter tree. Two locked API characteristics fall out (the "Emitter API" invariants): **(D1)** every artifact family declares its own transformer interface; **(D2)** an emitter speaks for exactly one subject (`subject(): Ui5ArtifactInterface`). With the transformer family-local, the neutral surface is fully determinable today; `EmitContext` may still gain fields as families arrive, but only ever backward-compatibly (it is `final readonly` — additive).

The neutral layer `LaravelUi5\Core\Ui5\Emit\`:

| Was | Now | Note |
|:---|:---|:---|
| `Ui5\Dashboard\DashboardElement` | `Ui5\Emit\WireElement` | marker: one node of a `{ui5.class: props}` tree. `Ui5\Controls\AbstractControl` now implements *this* — so `Controls\` (Part 2) imports nothing from `Dashboard\`. |
| `Ui5\Capabilities\DashboardElementEmitterInterface` | `Ui5\Emit\EmitterInterface` | `emit(EmitContext): WireElement` + `subject(): Ui5ArtifactInterface` |
| `Ui5\Dashboard\EmitContext` | `Ui5\Emit\EmitContext` | name unchanged |
| `Ui5\Dashboard\DashboardErrorSink` | `Ui5\Emit\EmitErrorSink` | per-child emit isolation |
| `Ui5\Dashboard\Disposition` | `Ui5\Emit\Veto\Disposition` | `Show \| Hide \| Lock` |
| `Ui5\Capabilities\DashboardVetoerInterface` | `Ui5\Emit\Veto\VetoerInterface` | `dispose(Ui5ArtifactInterface, ctx): Disposition` |
| `Ui5\Dashboard\DashboardVetoChain` | `Ui5\Emit\Veto\VetoChain` | union fold, most-restrictive-wins |

What **stays** in `Ui5\Dashboard\` (now *consuming* `Ui5\Emit\`): `Tile`, `Payload` (the tile-content/tile-root markers, now `extends Emit\WireElement`), `DefaultDashboardTransformer`, `Emitters\{Dashboard,Group,Tile,Card,Chart}Emitter`. The dashboard's `DashboardTransformerInterface` (in `Core\Contracts\`) keeps its name and its `Ui5DashboardInterface` input — per D1 it is family-local; only its return type changed to `Emit\EmitterInterface`.

**SemVer credit spent.** Consumer migration is `use`-line + symbol rename: any implementor of `DashboardVetoerInterface` (the host's `PortalLifecycleVetoer`, the SDK's future `AccessVetoer`) re-points to `Ui5\Emit\Veto\VetoerInterface`; any `DashboardElementEmitterInterface` to `Ui5\Emit\EmitterInterface`; `DashboardErrorSink`→`EmitErrorSink`, `DashboardVetoChain`→`VetoChain`, `DashboardElement`→`WireElement`; `EmitContext` and `Disposition` keep their class names, only the namespace moves. (The dashboard is the only consumer of these contracts today — the host's `PortalLifecycleVetoer` is the lone real implementor; the SDK does not participate in the Dashboard cycle.) No runtime behaviour, wire shape, or constructor signature changed.

### Moved + renamed

- `core/src/Ui5/Dashboard/DashboardElement.php` → `core/src/Ui5/Emit/WireElement.php` (`interface DashboardElement` → `WireElement`)
- `core/src/Ui5/Capabilities/DashboardElementEmitterInterface.php` → `core/src/Ui5/Emit/EmitterInterface.php` (→ `EmitterInterface`)
- `core/src/Ui5/Dashboard/EmitContext.php` → `core/src/Ui5/Emit/EmitContext.php` (name unchanged)
- `core/src/Ui5/Dashboard/DashboardErrorSink.php` → `core/src/Ui5/Emit/EmitErrorSink.php` (→ `EmitErrorSink`)
- `core/src/Ui5/Dashboard/Disposition.php` → `core/src/Ui5/Emit/Veto/Disposition.php` (name unchanged)
- `core/src/Ui5/Capabilities/DashboardVetoerInterface.php` → `core/src/Ui5/Emit/Veto/VetoerInterface.php` (→ `VetoerInterface`)
- `core/src/Ui5/Dashboard/DashboardVetoChain.php` → `core/src/Ui5/Emit/Veto/VetoChain.php` (→ `VetoChain`)
- `tests/Unit/Ui5/Dashboard/DashboardVetoChainTest.php` → `tests/Unit/Ui5/Emit/Veto/VetoChainTest.php`

### Changed

- `Ui5\Controls\AbstractControl`, `Dashboard\Payload`, `Dashboard\Tile` — now import + implement `Emit\WireElement`.
- `Core\Contracts\DashboardTransformerInterface` — return type → `Emit\EmitterInterface` (name + `Ui5DashboardInterface` input unchanged).
- `DefaultDashboardTransformer`, all `Dashboard\Emitters\*`, `DashboardController` — `use` lines re-pointed to the `Emit\` / `Emit\Veto\` contracts.
- Docblocks on the seven moved files de-dashboarded (categorical "dashboard-only" claims neutralized; dashboard retained as the canonical *first-consumer* example).

### Part 2 — Control vocabulary → `Ui5\Controls\`

The `Controls/` subtree and its property enums — a typed PHP mirror of the OpenUI5 control library, formerly under `Dashboard\Controls`. With Part 1 done, `AbstractControl` implements `Emit\WireElement` and the whole `Controls\` tree imports nothing from `Dashboard\`.

Three moves:

1. **`Ui5\Dashboard\Controls\*` → `Ui5\Controls\*`** — the 20 control DTOs move up one level, out of `Dashboard\`.
2. **Enums re-scoped to their true UI5 namespace** (verified against OpenUI5 1.136 for the ambiguous cases). The flat `Dashboard\Enums\` bucket masked that these belong to *different* libraries: `sap.m.*` (22), `sap.ui.core.ValueState` (1), `sap.f.cards.{HeaderPosition,SemanticRole}` (2), `sap.ui.integration.{CardDesign,CardDisplayVariant}` (2). Each enum now sits beside the control it parameterises (`Controls\Sap\M\Button` next to `Controls\Sap\M\ButtonType`). `Margin` and `ContentPadding` are *not* UI5 enums — they wrap UI5's predefined CSS style-classes — so they land in `Controls\Lux\` with our other authored types, distinct from the `Sap/` mirror.
3. **The editorial `Content/` segment is gone** — `Controls\Content\Sap\M\{Feed,Image,News,Numeric}Content` flatten to `Controls\Sap\M\*`. There is no `sap.m.content` namespace; the grouping broke the 1:1-with-UI5 rule and is removed.

**SemVer credit spent.** Mechanical consumer migration, `use`-line only: drop the `Dashboard\` segment from any `Controls\…` import; re-point enum imports per the table above (e.g. `Dashboard\Enums\ValueState` → `Controls\Sap\Ui\Core\ValueState`, `Dashboard\Enums\Margin` → `Controls\Lux\Margin`); flatten any `Controls\Content\Sap\M\…` to `Controls\Sap\M\…`. Constructor signatures, `ui5Class()` return strings, the wire shape, and all runtime behaviour are identical. Scaffolding stubs (`TileProvider.stub`, `ChartProvider.stub`) emit the new FQCNs.

### Moved

- `core/src/Ui5/Dashboard/Controls/` → `core/src/Ui5/Controls/` — 20 control DTOs (incl. `AbstractControl`, `Lux/Chart`, `Lux/ChartCanvas`, `Sap/F/AbstractCard`, `Sap/F/Cards/Header`, `Sap/F/GridContainer*`, the `Sap/M/*` controls, `Sap/Ui/Integration/Widgets/Card`). `ui5Class()` strings unchanged.
- `Controls/Content/Sap/M/{FeedContent,ImageContent,NewsContent,NumericContent}.php` → `Controls/Sap/M/*` — `Content/` segment removed.
- `Dashboard/Enums/*` (29 files) → per UI5 origin: 22 → `Controls/Sap/M/`, `ValueState` → `Controls/Sap/Ui/Core/`, `HeaderPosition`+`SemanticRole` → `Controls/Sap/F/Cards/`, `CardDesign`+`CardDisplayVariant` → `Controls/Sap/Ui/Integration/`, `Margin`+`ContentPadding` → `Controls/Lux/`. The `Dashboard/Enums/` directory is removed.
- `tests/Unit/Ui5/Dashboard/Controls/ControlSerializationTest.php` → `tests/Unit/Ui5/Controls/` — mirrors the source move.

## [0.9.41] - 2026-05-29 — SemVer credit spent

**Charts API namespace cleanup — `AbstractCard` + `Header` relocate to `Sap/F/*` to align with the mirror convention.** Two PHP class moves; zero wire-shape change; zero behavioural change. Per the project's namespace rule (`Sap/` = mirrors of stock UI5 wire classes; `Lux/` = our own compositions), `AbstractCard` and `Header` were structurally misplaced — both emit pure `sap.f.*` wire classes with no Lux-specific identity. Surfaced and settled in the V/R prep pass after 0.9.40 shipped: `Lux/AbstractCard.php` → `Sap/F/AbstractCard.php`, `Lux/Header.php` → `Sap/F/Cards/Header.php`. `Chart` and `ChartCanvas` stay in `Lux/` — `Chart` is the framework composition (extends `Sap\F\AbstractCard`, owns the `buildContent()` padding wrap from 0.9.40), `ChartCanvas` emits the Lux-namespaced `com.laravelui5.core.Chart` wire class. Future `Table`/`Report`/`Form` `extends AbstractCard` will sit in `Lux/` next to `Chart` — same pattern: Lux compositions extending the Sap/F abstract template.

**SemVer credit spent.** PHP FQCN change for two classes. Consumer migration is mechanical: any `use LaravelUi5\Core\Ui5\Dashboard\Controls\Lux\AbstractCard;` becomes `...\Sap\F\AbstractCard;`; any `...\Lux\Header;` becomes `...\Sap\F\Cards\Header;`. The wire shape, the constructor signatures, the runtime behaviour, the `ui5Class()` return strings are all identical — `ChartProvider` authoring sites at `new Chart(canvas: new ChartCanvas(...), header: new Header(...))` are unchanged except for the `use` lines.

### Moved

- `core/src/Ui5/Dashboard/Controls/Lux/AbstractCard.php` → `core/src/Ui5/Dashboard/Controls/Sap/F/AbstractCard.php` — namespace `LaravelUi5\Core\Ui5\Dashboard\Controls\Sap\F`. `ui5Class()` unchanged (`'sap.f.Card'`). The `Abstract` prefix flags "framework's abstract specialization of stock `sap.f.Card`," since UI5's `sap.f.Card` itself is concrete and our framework's template pattern (`abstract buildContent()`) makes our variant abstract. Accepted small honesty cost vs. scattering the `sap.f.*` DTOs across two namespaces.
- `core/src/Ui5/Dashboard/Controls/Lux/Header.php` → `core/src/Ui5/Dashboard/Controls/Sap/F/Cards/Header.php` — namespace `LaravelUi5\Core\Ui5\Dashboard\Controls\Sap\F\Cards`. `ui5Class()` unchanged (`'sap.f.cards.Header'`). The unambiguous case — pure `final readonly` mirror of `sap.f.cards.Header` + `sap.f.cards.BaseHeader`, no Lux behaviour, no framework hooks.

### Changed — internal references updated

- `Lux/Chart.php` — `use` statements updated to import `AbstractCard` from `Sap\F\` and `Header` from `Sap\F\Cards\`. Class body unchanged.
- `Sap/F/AbstractCard.php` — internal `use` for `Header` updated to `Sap\F\Cards\Header`.
- `Sap/F/Cards/Header.php` — internal `use` for `AbstractCard` updated to `Sap\F\AbstractCard`.
- `Emitters/ChartEmitter.php` — `use` for `Header` updated to `Sap\F\Cards\Header`.
- All `core/tests/Unit/Ui5/Dashboard/Controls/` — every test importing `AbstractCard` or `Header` updated to the new FQCNs.

## [0.9.40] - 2026-05-29 — SemVer credit spent

**Charts API Phase 5.1 follow-up + Phase 5.2 content padding + the two V/R-blocking PHPUnit gaps closed.** Three ride together because each rode the same EriChart V6 (b) browser smoke:

- **Cursor affordance on whole-card press.** `sap.f.Card` doesn't auto-change cursor when `attachPress` wires (unlike `sap.m.GenericTile` and `sap.m.Button`, which are inherently interactive). The Phase 5.1 whole-card press surface (`AbstractCard.parameters`, 0.9.39) shipped without a hover affordance — fixed in the dashboard walker's card-press arm.
- **Chart content padding (Phase 5.2).** Artifact-promoted Charts rendered flush against the `sap.f.Card` edge; the previous `Chart::buildContent()` returned `$this->canvas` verbatim. EriChart's 4×4 grid cell exposed the visual cramp. `Chart::buildContent()` now wraps the canvas in a `Bare`-rendered `sap.m.VBox` with `sapUiTinyMarginBeginEnd sapUiSmallMarginBottom` — tiny breathing room on the sides, small bottom-margin to balance the header's natural top spacing, `width/height: 100%` + `fitContainer: true` + `renderType: Bare` to keep the ECharts height chain intact (the per-flex-item wrapper that `Div` rendertype creates otherwise traps `height: 100%`).
- **PHPUnit gaps closed.** V5 dispatch arm + V2 generator coverage — see § Tests.

**SemVer credit spent.** The artifact-promoted-Chart wire envelope grew an inner `sap.m.VBox` layer between `sap.f.Card.content` and `com.laravelui5.core.Chart`. The Phase 4 / 0.9.37 contract named the two-layer envelope `{"sap.f.Card": {..., "content": {"com.laravelui5.core.Chart": {...}}}}` as Frozen; this patch grows it to a three-layer envelope `{"sap.f.Card": {..., "content": {"sap.m.VBox": {items: [{"com.laravelui5.core.Chart": {...}}], …}}}}`. Authoring code is unaffected — providers still construct `new Chart(canvas: new ChartCanvas(...))`. The Tier-3 walker materialises the extra layer transparently (`sap.m.VBox` was already in its vocabulary; no widening). Consumers that pattern-matched the wire shape verbatim (Karma walker round-trip tests; the dispatch-arm assertion shipped in this patch's other half) need to walk through `content.sap.m.VBox.items[0]` to reach the canvas now. The secondary inline-`ChartCanvas` path (inside a Card's content tree) is unaffected — those compositions bypass `Chart` entirely.

### Changed — Chart content padding (wire-shape change, Phase 5.2)

- `Lux/Chart.php` `buildContent()` — wraps `$this->canvas` in a `sap.m.VBox` with `class: 'sapUiTinyMarginBeginEnd sapUiSmallMarginBottom'`, `width: '100%'`, `height: '100%'`, `fitContainer: true`, `renderType: FlexRendertype::Bare`. The VBox lands as the value of `sap.f.Card.content`; the canvas lands as `VBox.items[0]`.
- Settled empirically against EriChart's 4×4 grid cell. The `Bare` rendertype is load-bearing — `Div` (the FlexBox default) emits a per-item `<div class="sapMFlexItem">` wrapper that has no explicit height, breaking the `height: 100%` chain ECharts needs.

### Fixed

- `ui5-core-lib/src/controls/Dashboard.js` `_wireActions` card-press arm — after `card.attachPress(...)`, sets `cursor: pointer` on the rendered DOM via an `onAfterRendering` delegate. Clickable cards now read as clickable; survives re-renders. Mirror change in the bundled `core/resources/ui5/controls/Dashboard-dbg.js` and the flat `core/resources/ui5/Dashboard-dbg.js`. The minified `.js` and `.js.map` bundles need a re-bundle from `ui5-core-lib` before the tag.

### Tests

- `tests/Feature/Integration/DashboardTransformerTest.php` — new test exercising the `Ui5ChartInterface` arm of `DefaultDashboardTransformer::buildLeafEmitter`. Asserts the two-layer wire envelope (`{"sap.f.Card":{..., "content":{"com.laravelui5.core.Chart":{...}}}}`), the inner engine + option round-trip, and ChartEmitter's header auto-population from the artifact's title + description. Closes the V5 dispatch-test gap.
- `tests/Feature/Commands/GenerateUi5ChartCommandTest.php` — new test covering the `ui5:chart` generator. Asserts the scaffolded chart class + provider land at the expected paths with correct PHP namespace, UI5 namespace, title, description, and class structure. Covers happy path, defaulted title/description, no-overwrite, and missing-app-module failure paths. Closes the V2 generator-test gap. Mirrors the `ui5:slot:*` artisan-command convention — the only other command test in the suite.
- `tests-fixture/src/Dashboards/Charts/HelloChart.php` + `HelloChartProvider.php` — worked-example chart fixture mirroring `HelloTile`. Registered on `HelloModule::getCharts()`; added to `MainGroup::getChildNamespaces()`. Drives the new dispatch test.
- `tests/Feature/Integration/DashboardControllerTest.php` — grid-items count assertion bumped 1 → 2 to reflect MainGroup's new `[HelloTile, HelloChart]` composition; tile remains items[0] so the existing tile-specific assertions stay valid.
- `tests/Unit/Ui5/Dashboard/Controls/ControlSerializationTest.php` — `Chart` Phase-5.2 wire-shape assertion: the `Chart serializes as sap.f.Card wrapping the provider-composed ChartCanvas` test renamed to `Chart wraps the ChartCanvas in a padded VBox inside sap.f.Card.content` and now asserts the VBox properties (`class`, `width`, `height`, `fitContainer`, `renderType`) + the canvas at `items[0]`. The other Chart tests (`Chart omits all optional sap.f.Card fields when null`, header / layoutData / parameters tests) read the new content shape transparently — no further updates needed there.

### Notes — V/R scheduling

With this patch the Charts API has every V/R gate met except V6 (b) — second use-case-diverse authoring, lined up as pragmatiqu.io's `EriChart`. Once V6 (b) lands, the V/R sweep runs against the Frozen-target surface — see `docs/meta/specs/core-charts-api-v0.9.md` § V/R review scheduling. The V7 customer docs (`docs/core/backend/chart.md` + `docs/core/frontend/lux-chart.md`) shipped 2026-05-29 in the docs repo.

## [0.9.39] - 2026-05-28 — SemVer credit spent

**Charts API Phase 5.1 — whole-card press for `AbstractCard` (`Chart.parameters`).** Adds the Tile-precedent press-intent seat at the `AbstractCard` layer: a `parameters: ?array = null` constructor field on `Lux/AbstractCard` that emits as a top-level property on the `sap.f.Card` wire envelope. The Dashboard control's `_wireActions` walk gains a fourth arm — `findAggregatedObjects(...sap.f.Card)` (filtered to exclude `sap.ui.integration.widgets.Card`) — that checks `card.data("parameters")` and routes the card's `press` event through the dashboard `action` channel. **Single-action drill-in cards** now have a clean affordance (whole card becomes the click target) without the title/toolbar split that Header.toolbar buttons impose on narrow grid cells. Surfaced during EriChart adoption: the workspace-link UX wanted a single primary action, and the toolbar layout looked visually divided. Spec: `docs/meta/specs/core-charts-api-v0.9.md` § Whole-card press (D11).

**SemVer credit spent**: `Lux/AbstractCard` and `Lux/Chart` constructors gain a `?array $parameters = null` parameter between `layoutData` and `class`. Existing call sites are unaffected (the parameter is optional and named).

### Added — AbstractCard.parameters

- `Lux/AbstractCard.php` — constructor gains `?array $parameters = null`; emit-when-set in `optionalProperties()`. Every future `AbstractCard` subclass (Chart now; Table / Report / Form later) inherits the whole-card press-intent seat with zero per-subclass work. Docblock extended to name the convention + the Header.toolbar coexistence (D10 stays valid for multi-action; D11 is the single-action complement).
- `Lux/Chart.php` — constructor pass-through; `withHeader()` preserves `parameters` through the immutable copy.

### Changed — JS walker

- `ui5-core-lib/src/controls/Dashboard.js` `_wireActions` — fourth arm added. Finds `sap.f.Card` instances that are NOT `sap.ui.integration.widgets.Card` (the integration-card branch above handles Custom actions via `attachAction`); reads `card.data("parameters")`; attaches a press handler that fires the dashboard `action` event with `{child, type: "Press", parameters}`. Same pattern as the Tile + Button arms.
- No vocabulary widening — `sap.f.Card` is already in the map from Phase 4.

### Tests

- `ControlSerializationTest.php` — 2 new chart-vocabulary tests: parameters verbatim on the wire; key omitted when null. The `withHeader` test gains a parameters-preservation assertion. 29 chart-vocabulary tests total; full Pest suite passes 335/335 (1 skipped).

### Notes — D10 and D11 coexist

D10 locked Header.toolbar as the action surface for **multi-action discrete buttons** (refresh + configure + export). D11 (this batch) adds whole-card press for **single-action drill-in cards**. They are complementary, not overlapping:

- One primary action per card → `Chart.parameters` (whole card is the click target; no visible button competes with the title).
- Multiple discrete actions per card → `Header.toolbar` (OverflowToolbar with Buttons, collapse to `⋯` on narrow cells).
- Both on the same card → fine, but unusual; the card-press fires on body click while toolbar-button presses fire on button click. Different click sources, different events.

## [0.9.38] - 2026-05-28 — SemVer credit spent

**Charts API Phase 5 — header-action surface (`Header.toolbar` + `Button.parameters` press wiring).** Adds the SAP-Fiori-idiomatic action seat for artifact-promoted Charts (and every future AbstractCard subclass): a `toolbar: ?AbstractControl` aggregation on `Lux/Header` (mirroring `sap.f.cards.BaseHeader.toolbar`), two new DTOs (`Lux/Sap/M/OverflowToolbar` and `Lux/Sap/M/Button`), and a `ButtonType` enum mirroring `sap.m.ButtonType`. Buttons placed in the header toolbar carry the same `parameters` press-intent convention Tiles use; the Dashboard control's `_wireActions` walk extends to find `sap.m.Button` controls and route their press through the dashboard `action` event channel. Spec: `docs/meta/specs/core-charts-api-v0.9.md` § Header action surface (Phase 5).

**SemVer credit spent**: `Lux/Header.php`'s constructor gains a trailing `?AbstractControl $toolbar = null` parameter. Existing call sites are unaffected (the parameter is optional and last). The Dashboard wire walker grows 14 → 16 (`sap.m.OverflowToolbar` + `sap.m.Button`).

**Architectural lock**: the action surface for artifact-promoted Charts (and future Table / Report / Form subclasses of `AbstractCard`) is now sealed at this shape — actions live in the header's toolbar slot, never in a footer (sap.f.Card has no footer aggregation by design). Open composition for chart-plus-arbitrary-content remains future Pane/Cell artifact work; not v0.9.

### Added — DTOs

- `Lux/Sap/M/Button.php` — `final readonly`, mirrors `sap.m.Button`. Fields: `text` / `type` / `icon` / `enabled` / `visible` / `tooltip` / `parameters` / `class`. All emit-when-set. `parameters` is the press-intent seat (free-form array, walker stashes it as custom data, dashboard re-fires it on press).
- `Lux/Sap/M/OverflowToolbar.php` — `final readonly`, mirrors `sap.m.OverflowToolbar`. Single aggregation: `content: array<AbstractControl>`. Minimal surface; UI5 defaults accepted for design / style / height / active until a real consumer asks for tighter control.
- `Dashboard/Enums/ButtonType.php` — string-backed enum mirroring `sap.m.ButtonType` (`Default`, `Back`, `Accept`, `Reject`, `Transparent`, `Ghost`, `Up`, `Unstyled`, `Emphasized`, `Critical`, `Negative`, `Success`, `Neutral`, `Attention`).

### Changed (breaking) — Header.toolbar

- `Lux/Header.php` — constructor gains a trailing optional `?AbstractControl $toolbar = null`. `optionalProperties()` emits the toolbar as a nested control envelope when set. Docblock + per-field notes updated.

### Changed — JS walker

- `ui5-core-lib/src/controls/Dashboard.js` — vocabulary widens 14 → 16: adds `"sap.m.OverflowToolbar": OverflowToolbar` and `"sap.m.Button": Button`. The `_wireActions` walk gains a third arm (after Card + GenericTile): `findAggregatedObjects(...sap.m.Button)` → check `button.data("parameters")` → if present, `attachPress` that fires the dashboard `action` event with `{child, type: "Press", parameters}`. Same exact pattern as the existing Tile arm.
- Chart-press wiring is unchanged — `<lux:Chart>`'s `press` event stays consumer-handled per the spec's `§ Dashboard integration / Interaction with the Dashboard action event` boundary. Charts can live outside dashboards (future Reports); coupling their press into the dashboard action channel would over-bind two distinct surfaces. The Button-in-toolbar pattern is the explicit action surface; chart-press stays for data-point interactions.

### Tests

- `ControlSerializationTest.php` — 9 new tests: 2 for `Header.toolbar` (emits the nested OverflowToolbar envelope; omits the key when null), 5 for `Button` (omits-when-empty; text+icon+tooltip round-trip; type-enum backing-string; parameters verbatim; boolean false explicit), 2 for `OverflowToolbar` (empty content; multi-Button content). 27 chart-vocabulary tests total; full Pest suite passes 333/333 (1 skipped).

## [0.9.37] - 2026-05-28 — SemVer credit spent

**Charts API — `Chart` constructor takes an explicit `canvas: ChartCanvas` slot.** Refactors the 0.9.36 `Chart` shape so the provider composes the full two-DTO tree visibly, matching the Tile precedent where providers build `GenericTile → TileContent[] → payload` explicitly. The pre-0.9.37 Chart accepted `engine` + `option` directly and auto-built the inner ChartCanvas in `buildContent()`; that cleverness hid the two-layer structure (sap.f.Card outer + com.laravelui5.core.Chart inner) at every authoring site. 0.9.37 lifts the canvas into a constructor slot so the structure is visible at every `new Chart(...)`. Wire output is unchanged.

**SemVer credit spent**: `Chart`'s constructor signature changes from `(string $engine, array $option, ?Header $header = null, …)` to `(ChartCanvas $canvas, ?Header $header = null, …)`. Existing providers update one line at the call site: `new Chart(engine: 'echarts', option: [...])` → `new Chart(canvas: new ChartCanvas(engine: 'echarts', option: [...]))`. No wire-shape change; no schema migration. pragmatiqu.io's `RevenueTrendChartProvider` is the only known consumer; updates in the same window as the Core tag.

### Changed (breaking)

- `src/Ui5/Dashboard/Controls/Lux/Chart.php` — class is now `final readonly` (was `final` only). Constructor takes a required `ChartCanvas $canvas` as the first parameter; `engine` + `option` + the inner `class` field are gone from Chart and live solely on ChartCanvas. `buildContent()` returns `$this->canvas` verbatim. `withHeader()` updated to preserve the canvas reference through the immutable copy.
- `resources/stubs/ChartProvider.stub` — generator stub updated. New scaffolded providers return `new Chart(canvas: new ChartCanvas(engine, option))`.

### Tests

- `ControlSerializationTest.php` — the six `Chart` outer-wrapper tests migrated to the new shape. All `new Chart(engine, option, …)` call sites become `new Chart(canvas: new ChartCanvas(engine, option), …)`. Wire-shape assertions unchanged. The `withHeader` test gains a canvas-preservation assertion (`$rehoused->canvas === $canvas`). 18 chart-vocabulary tests still green; full Pest suite passes 324/324 (1 skipped).

### Notes

- The 0.9.36 `Chart(engine, option)` shape is **gone** at 0.9.37, not deprecated. There's only one consumer (pragmatiqu.io's `RevenueTrendChartProvider`) and the migration is mechanical — no value to a deprecation window. Spec § Open follow-ups and § Implementation plan / Phase 4 updated to describe the explicit-canvas shape.
- The pattern generalises for future structured dashboard elements (Table, Report, Form): each extends `AbstractCard`, takes its specialised content as a required constructor slot, returns it from `buildContent()`. No clever auto-build at the AbstractCard layer.

## [0.9.36] - 2026-05-28 — SemVer credit spent

**Charts API Phase 4 — `AbstractCard` hierarchy + `ChartCanvas` split.** The artifact-promoted Chart now wraps in a `sap.f.Card` envelope via a new `AbstractCard` hierarchy that mirrors `sap.f.Card` + `sap.f.CardBase`. Two-DTO split: `Chart extends AbstractCard` is the outer envelope (PHP class name preserved); `ChartCanvas` is the inner engine-rendered DTO (wire class `com.laravelui5.core.Chart` unchanged) — relocated from the previous `Lux/Chart.php` content. A new `Lux/Header.php` mirrors `sap.f.cards.Header` + `sap.f.cards.BaseHeader`. The pattern is the **template for future structured dashboard elements** (Table, Report, Form — each `extends AbstractCard`). Spec D9: `docs/meta/specs/core-charts-api-v0.9.md` § Decisions / D9 + § Implementation plan / Phase 4. Design diary: `docs/meta/diary/CHARTS_API_PHASE_4_DESIGN.md`.

**SemVer credit spent**: the artifact-promoted Chart's wire shape changes from a single-layer `{"com.laravelui5.core.Chart": {...}}` to a two-layer `{"sap.f.Card": {..., "content": {"com.laravelui5.core.Chart": {...}}}}`. The `Lux\Chart` PHP class signature changes (now extends `AbstractCard`, takes optional `Header` + `headerPosition` + `width` + `height` + `semanticRole` + `layoutData` + `class`); the inner field shape (`engine` + `option` + `class`) relocates to the new `Lux\ChartCanvas` class. Downstream provider code typically does not change — `new Chart(engine: '...', option: [...])` still works — but consumers that constructed the pre-Phase-4 Chart to compose it INLINE inside a parent Card's content tree must switch to `new ChartCanvas(...)` (the secondary authoring path; see spec § Composition).

### Added — DTOs

- `Lux/AbstractCard.php` — `abstract readonly` parent for any structured dashboard element that lands as a `sap.f.Card`. Properties: `header` / `headerPosition` / `width` / `height` / `semanticRole` / `layoutData` / `class`. `ui5Class()` is `final` and returns `'sap.f.Card'`. Subclasses implement abstract `buildContent(): ?AbstractControl`.
- `Lux/Header.php` — `final readonly` mirror of `sap.f.cards.Header` + `sap.f.cards.BaseHeader`. Properties: `title`, `titleMaxLines`, `subtitle`, `subtitleMaxLines`, `statusText`, `iconSrc`, `iconInitials`, `iconAlt`, `iconDisplayShape`, `iconBackgroundColor`, `iconVisible`, `iconSize`, `iconFitType`, `dataTimestamp`, `statusVisible`, `wrappingType`, `href`, `target`. Wire class `'sap.f.cards.Header'`. All fields optional.
- `Lux/ChartCanvas.php` — `final readonly` inner DTO. Receives the relocated `engine` + `option` + `class` fields from the pre-Phase-4 `Lux\Chart`. Wire class `'com.laravelui5.core.Chart'` unchanged. What the `<lux:Chart>` client control materialises.
- 6 new enums under `Dashboard/Enums/`: `HeaderPosition` (sap.f.cards.HeaderPosition), `SemanticRole` (sap.f.cards.SemanticRole), `AvatarShape` (sap.m.AvatarShape), `AvatarColor` (sap.m.AvatarColor), `AvatarSize` (sap.m.AvatarSize), `AvatarImageFitType` (sap.m.AvatarImageFitType). String-backed, mirror the UI5 enum values.

### Changed (breaking) — Lux\Chart repurposed

- `Lux/Chart.php` now `final readonly extends AbstractCard`. Constructor: `(string $engine, array $option, ?Header $header = null, ?HeaderPosition $headerPosition = null, ?string $width = null, ?string $height = null, ?SemanticRole $semanticRole = null, ?GridContainerItemLayoutData $layoutData = null, ?string $class = null)`. `buildContent()` constructs a `ChartCanvas` from `$this->engine` + `$this->option`. New `withHeader(?Header): self` immutable update for emitter use.
- `ChartEmitter` post-call substitution: when the provider returned a Chart with `header: null`, auto-populates `Header(title: $chart->getTitle(), subtitle: $chart->getDescription())` via `Chart::withHeader()`. Providers can pass an explicit Header to suppress the auto-fill.

### Tests

- `ControlSerializationTest.php` — Chart vocabulary describe block split into four: `ChartCanvas` (4 tests, migrated from the pre-Phase-4 Chart inner-shape tests); `Chart` outer wrapper (6 new tests covering sap.f.Card envelope, optional-field omission, header round-trip, layoutData emission, enum serialization, `withHeader` immutability); `Header` (5 new tests covering full property round-trip including BaseHeader fields + boolean false explicit emission); `AbstractCard` base behaviour (3 tests via an inline test subclass). 18 chart-vocabulary tests total; full Pest suite passes 324/324 (1 skipped).
- `VendorAssetControllerTest.php` deleted (orphan from 0.9.35; controller was removed but test wasn't swept).

## [0.9.35] - 2026-05-28 — SemVer credit spent

**Bootstrap mechanism pivot — vendor blob removal + SDK ownership of the shell-fragment seat.** Two cuts at one tag. First: rolls back the `VendorAssetController` + bundled ECharts shipped in 0.9.33 — bundling third-party JS into Core is the anti-pattern, host loads via CDN/import map/its own asset pipeline. Second: relocates the SDK-flavoured `@IncludeIfSdk` directive off Core (where it had no business sitting per the Core stateless rule) and reshapes the bootstrap's consumer-extension contract around plain Laravel `@includeIf` on bare view names. Spec: `docs/meta/specs/core-bootstrap-mechanism-v0.9.md`.

**SemVer credit spent**: removes the `@IncludeIfSdk` directive, the `/ui5/vendor/*` route, the `VendorAssetController` class, the bundled ECharts assets, and the `ui5::` view namespace registration. Any host currently relying on the `@IncludeIfSdk('ui5::head'|'ui5::body')` injection now lives downstream in the SDK as `@includeIfSdk('ui5::head'|'ui5::foot')` and is wired through a consumer-owned `resources/views/ui5/{head,foot}.blade.php` template (see migration below).

### Removed — vendor surface

- `src/Controllers/VendorAssetController.php` — deleted.
- `Route::prefix('ui5/vendor')->group(...)` block in `Ui5CoreServiceProvider::boot()` — deleted.
- `resources/assets/echarts/6.1.0.{min.js,min.js.map,LICENSE,mjs}` — deleted (and the surrounding `resources/assets/` tree).
- `<script src="/ui5/vendor/echarts/6.1.0.min.js"></script>` from `resources/views/index.blade.php` — deleted.

### Removed — bootstrap surface

- `Blade::directive('IncludeIfSdk', ...)` block in `Ui5CoreServiceProvider::boot()` — deleted. The directive moves to the SDK (renamed to lowercase-initial `@includeIfSdk` per Laravel convention).
- `$this->loadViewsFrom(__DIR__ . '/../resources/views', 'ui5');` in `Ui5CoreServiceProvider::boot()` — deleted. Core's `ui5::` view namespace no longer exists. The SDK now owns the `ui5::` namespace via strict `View::addNamespace(...)` for its shell-fragment payloads.
- Unused imports: `Illuminate\Support\Facades\Blade`, `LaravelUi5\Core\Controllers\VendorAssetController`.

### Changed (breaking) — bootstrap-extension contract

- `resources/views/index.blade.php` — the two extension seats are now plain `@includeIf('ui5.head')` (top of `<head>`) and `@includeIf('ui5.foot')` (end of `<body>`). Bare view names — Laravel's view-finder resolves them against the *host app's* `resources/views/`, missing-file silently no-ops. Replaces `@IncludeIfSdk('ui5::head')` / `@IncludeIfSdk('ui5::body')`.
- `src/Controllers/IndexController.php` — `response()->view('ui5::index', ...)` becomes `response(View::file(__DIR__ . '/../../resources/views/index.blade.php', ...))`. The view is rendered from its absolute path; there's no longer a namespace to register for a single template.

### Migration

A host currently using SDK shell fragments adds two files:

```blade
{{-- resources/views/ui5/head.blade.php --}}
@includeIfSdk('ui5::head')

{{-- resources/views/ui5/foot.blade.php --}}
@includeIfSdk('ui5::foot')
```

A host that wants to inject third-party JS (ECharts, etc.) puts the `<script>`/`<link>` tags in the same templates — adjacent to (or instead of) the `@includeIfSdk(...)` call. No vendor publish step. No Core route involvement.

A Core-only host (no SDK) creates neither file — `@includeIf(...)` silently does nothing.

## [0.9.34] - 2026-05-28 — SemVer credit spent

**Charts API — artifact-type promotion.** Promotes Chart from the embedded-element DTO shipped in 0.9.33 to a full artifact type (Tile-mirror: artifact + provider + emitter). A Chart can now be registered on a module and listed alongside Tiles and Cards in a Dashboard Group's `getChildNamespaces()`. The `Chart` DTO itself (the wire shape from 0.9.33) is unchanged; it is now the value the new `ChartProviderInterface::getChart()` returns.

**SemVer credit spent**: `Ui5ModuleInterface` gains `getCharts(): array`. `AbstractUi5Module` provides the `return []` default, so existing module subclasses are unaffected; direct `Ui5ModuleInterface` implementations (rare) get a compile-time signal.

### Added — artifact surface

- `ArtifactType::Chart = 15`. Label "Chart"; `isAccessible(): true`; embedded-only (no `routePrefix`).
- `Ui5ChartInterface extends Ui5ArtifactInterface, SlottableInterface` — adds `getChartProvider(): ChartProviderInterface`.
- `ChartProviderInterface::getChart(array $boundParams, Ui5ContextInterface $context): Chart` — executor seat, mirrors `TileProviderInterface`.
- `AbstractUi5Chart implements Ui5ChartInterface` — const-backed identity via `HasArtifactIdentity`; default empty `getRequiredSlots()` / `getSlotProposals()`.
- `ChartEmitter` — resolves required slots via `EmitContext->pipeline`, delegates to the provider, returns the `Chart` DTO. Same shape as `TileEmitter`.
- `DefaultDashboardTransformer::buildLeafEmitter` — `Ui5ChartInterface` branch added (Tile / Card / Chart trio).

### Added — generator surface

- `ui5:chart {App}/{Name} [--title=…] [--description=…]` — scaffolds `ui5/{App}/src/Charts/{Name}Chart.php` + `Provider/{Name}ChartProvider.php`. Mirrors `ui5:tile`.
- `resources/stubs/Ui5Chart.stub` + `resources/stubs/ChartProvider.stub` — artifact + provider templates. Provider stub ships a placeholder bar-chart `option` for immediate visual feedback.
- `Ui5ModuleApp.stub` + `Ui5ModuleLib.stub` — both gain `getCharts(): array { return []; }`.

### Changed — SemVer credit spent

- `Ui5ModuleInterface::getCharts(): array` — new method on the interface. `AbstractUi5Module::getCharts()` returns `[]` by default and is threaded into `getAllArtifacts()`.

## [0.9.33] - 2026-05-28

**Charts API v0.9.** Adds the `Chart` embedded-element DTO (server), the `com.laravelui5.core.Chart` control (client), and Core-hosted ECharts 6.1.0 for dashboard cards and tiles. Spec: `docs/meta/specs/core-charts-api-v0.9.md`.

### Added

- `Chart` DTO at `src/Ui5/Dashboard/Controls/Lux/Chart.php` — `final readonly extends AbstractControl`. Constructor `(string $engine, array $option, ?string $class = null)`. Wire shape `{"com.laravelui5.core.Chart": {"engine": "echarts", "option": {...}}}`; `class` emit-when-set.
- `com.laravelui5.core.controls.Chart` control (ui5-core-lib) — `option` property, `press` event (componentType / seriesIndex / dataIndex / data / value / name). Setter override pushes updates to the live ECharts instance without re-render. Reads `window.echarts`. `laravelui5-horizon` theme (SAP Horizon palette, transparent background) registered with ECharts at first construction.
- `ChartRenderer` — mount-point `<div>` filling its container.
- Dashboard `treeWalker` vocabulary widens 11 → 12 with `com.laravelui5.core.Chart`.
- `VendorAssetController` serving Core-hosted third-party JS bundles from `resources/assets/` (read `__DIR__`-relative, so works in path-loaded and vendor-installed setups alike). Route `Route::get('vendor/echarts/{file}', VendorAssetController::class)` whitelisted to `\d+\.\d+\.\d+\.(min\.js(\.map)?|LICENSE|mjs)`. Long-lived caching (`Cache-Control: public, max-age=31536000, immutable`) — the version is baked into the URL, so version bumps land at new paths.
- Vendored **ECharts 6.1.0** at `resources/assets/echarts/6.1.0.min.js` (+ `.map`, `.LICENSE`, `.mjs`). Apache 2.0, ASF top-level project. Served at `/ui5/vendor/echarts/6.1.0.min.js`. Host apps add one `<script src="/ui5/vendor/echarts/6.1.0.min.js">` line to their index template before UI5 boots — Chart reads `window.echarts`.
- `resources/assets/echarts/README.md` — internal note documenting where to source future ECharts updates and how to drop the files into this directory.

### Tests

- 4 Pest tests (`tests/Unit/Ui5/Dashboard/Controls/ControlSerializationTest.php`) — wire shape, `class` emission, verbatim data-point pass-through, empty-option degradation.
- 10 Karma+QUnit tests (`ui5-core-lib/test/qunit/controls/chart/Chart.qunit.js`) — `echarts.init` invocation with theme, `setOption` with `notMerge`, in-place updates via the setter override, press forwarding (series + legend/axis), the missing-`window.echarts` case.
- Controller test for `VendorAssetController` — happy-path serve (200 + correct Content-Type + long-cache headers), whitelist rejection (non-matching filenames → 404), missing-file path (404 with no body leakage).

## [0.9.32] - 2026-05-28

**Generator parser fix — `getNamespaceFromFile` survives `const NAMESPACE`.** Surfaced while exercising the Library API V6 closer (a second authoring of `Ui5LibraryInterface` via `ui5:lib --create` + `--refresh` on a fresh `~/laravelui5-host/ui5/Address/`). `--refresh` rewrote line 3 of the generated `AddressLibrary.php` as `namespace Pragmatiqu\Address  'io.pragmatiqu.address';` — invalid PHP. Diagnosis: PHP keywords tokenize case-insensitively, so the `const NAMESPACE` introduced by `HasArtifactIdentity` (0.9.26) emits a second `T_NAMESPACE` token that `BaseGenerator::getNamespaceFromFile` re-entered on, appending the const's value onto the captured PHP-namespace string. Without this fix every `--refresh` against a 0.9.26+ artifact file corrupts the same way. Fix: `break` the outer loop after the first inner-loop match — PHP only allows one namespace declaration per file. One-line change in `src/Commands/BaseGenerator.php`. Library API V6 closer unblocked.

### Fixed

- `BaseGenerator::getNamespaceFromFile()` no longer re-enters its scan on the `const NAMESPACE` token (introduced by `HasArtifactIdentity` in 0.9.26 across all 9 artifact bases). Add an outer-loop `break` after the first `T_NAMESPACE` declaration completes. Affects every `ui5:*` generator that reads the PHP namespace back from an existing artifact file on `--refresh`.

## [0.9.31] - 2026-05-28 — SemVer credit spent

**Application API acceptance review (#7) + a Dashboard-surface cleanup.** The
seventh and last artifact-API surface review (`docs/meta/specs/core-application-api-v0.9.md`)
landed two paperwork gaps and shipped them here. Same shape as the Module API
review's 0.9.29 follow-up: V2 generator drift caught in-review, V4 failure-path
test added at the right layer. The contract substance was already clean —
Keystone ② (0.9.26) and namespace single-sourcing (0.9.28) made the surface
ready; this batch closes the mechanical gaps. **Signs the Application API at
1.0 Frozen.** Rides one structural cleanup that earned the SemVer-credit-spent
heading: `DefaultDashboardTransformer` moved into `Ui5\Dashboard\`.

### Changed

- **`Ui5App.stub` no longer declares `getType()`.** `AbstractUi5App::getType()`
  already returns `ArtifactType::Application` for every app; the stub redundantly
  re-emitted the method on every generated subclass — same drift class as
  0.9.29's `getName()` cleanup on the module stubs (a base method needlessly
  re-declared by the generator). Dropped the method + the `ArtifactType` import
  from `resources/stubs/Ui5App.stub`. Generator-only — generated apps lose a dead
  override; existing hand-written `getType()` declarations still work
  (`HasArtifactIdentity` doesn't govern type; the base does).

### Added

- **`ManifestController` failure-path test.** `tests/Feature/Integration/ManifestControllerTest.php`
  gains `it throws MissingManifestException when getManifestPath points at a
  missing file` — asserts the controller's defined failure mode (a mocked
  `Ui5AppInterface` returning a non-existent manifest path triggers the
  cataloged exception). Closes V4 of the Application API acceptance review;
  V5 (full-dispatch) was already covered by the happy path.

### Changed (breaking — namespace move; in-house consumer scope)

- **`DefaultDashboardTransformer` moved from `LaravelUi5\Core\Services\` to
  `LaravelUi5\Core\Ui5\Dashboard\`.** The transformer originally landed in
  `Services\` on the assumption that consumers (SDK, host packages) would
  subclass it; that override pathway never materialised and is not anticipated
  — the seat is Core-owned and fixed. Moving it joins the rest of the Dashboard
  API surface (the emitters, the veto chain, the disposition enum) under the
  same namespace, where it belongs. All in-house references (the
  `Ui5CoreServiceProvider` binding, four Dashboard tests, five docblock
  `@see` references in interfaces/exceptions) updated. Consumers outside the
  workspace that import the FQCN: update the `use` statement.

**Dashboard API follow-up.** Removed obsolete interfaces and classes. 

### Removed

- **Stale pre-Dashboard-API render cluster.** Deleted the
  `LaravelUi5\Core\Ui5\Data` namespace (`Payload` + `NumericContentData`) and its
  sole consumer `LaravelUi5\Core\Ui5\Capabilities\RenderableInterface`
  (`render(Payload): string`). This was the pre-0.9.15 "an artifact renders itself
  to an XML fragment from a `Payload` DTO" model — superseded wholesale by the
  Dashboard API's emitter + typed-control DTOs (`Ui5\Dashboard\Payload` + the
  `…Content` controls). Zero references remained; 305 tests green.
- **`Ui5\Capabilities\ResolvableInterface`.** Sibling relict of the same
  pre-Dashboard-API render cluster — `resolve(): string` returning a UI5 XML
  fragment for inclusion in a `<core:FragmentDefinition>`, with `<GenericTile>`
  named in the docblock (the Tile-API-superseded shape). Orphaned by the 0.9.28
  `Ui5Element` removal; workspace-wide grep confirmed zero implementers and zero
  PHP references. Deleted with the same justification as `RenderableInterface`
  above.

## [0.9.29] - 2026-05-27

**Generator follow-up to the 0.9.28 namespace single-sourcing.** The
`Ui5ModuleApp` / `Ui5ModuleLib` stubs still emitted the removed `getName()`, so
`ui5:app` / `ui5:lib` reintroduced the very namespace duplication 0.9.28 deleted.
Caught by the Module API acceptance review (`docs/meta/specs/core-module-api-v0.9.md`).
Generator-only — no consumer ripple.

### Changed

- **Module stubs no longer declare a namespace method.** Both
  `resources/stubs/Ui5ModuleApp.stub` and `Ui5ModuleLib.stub` drop `getName()`;
  `AbstractUi5Module` derives the namespace from the module's root artifact, so a
  generated module declares none of its own.

## [0.9.28] - 2026-05-27 — SemVer credit spent

**Acceptance batch: Action signed + two pre-1.0 structural fixes.** Three things
ride this release: (1) the **Action API** acceptance review signed the surface
**Frozen** (the dossier is `docs/meta/specs/core-action-api-v0.9.md`; only a test
landed); (2) the **Module namespace** is now single-sourced (breaking); (3) the
UI5 control **enums relocated** to `Ui5\Dashboard\Enums` (breaking). Both breaking
changes spend SemVer credit on the 0.9.x line. **Downstream consumers must:**
(1) rename module `getName()` → `getNamespace()` call sites (concrete modules may
drop the method — the base now derives it from the root artifact); (2) update enum
imports `Ui5\Enums\*` → `Ui5\Dashboard\Enums\*`; (3) update the moved assets trait
`Core\Traits\HasAssetsTrait` → `Core\Ui5\Concerns\HasAssets` (the trait reorg
below — same API). **`laravelui5/auth` must be migrated + re-published before any
app on it can boot against 0.9.28** (it uses both the old trait and module
`getName()`).

### Added

- **`tests/Feature/Integration/ActionDispatchFailureTest.php`** — a Core-level
  dispatch failure-path test (unknown action namespace → 404 via
  `MissingArtifactException`), closing the acceptance **V4** floor for the Action
  surface (Core previously had only the happy-path `ActionDispatchControllerTest`;
  failure paths were covered only in consumer suites). Test-only; no contract or
  behaviour change.
- **type declartion** to comment of `HasArtifactIdentity`.

### Changed (breaking)

- **Module namespace is single-sourced.** `Ui5ModuleInterface::getName()` →
  **`getNamespace()`**. A module no longer declares its own namespace — the
  **root artifact (App/Library) owns it** (its `NAMESPACE` const), and
  `AbstractUi5Module::getNamespace()` derives it via
  `getArtifactRoot()->getNamespace()`, so the two can never drift (the old
  duplication is gone). `AbstractUi5App`'s constructor now sources its namespace
  from `$this->getNamespace()` instead of `$module->getName()` — which both
  flips the ownership direction and removes the construction-time back-reference
  (so the module's derivation can't recurse into the App ctor). `CoreLibrary`
  now owns `const NAMESPACE = 'com.laravelui5.core'` (it previously derived from
  the module — the reversed, cycle-prone direction); `CoreModule` derives, and
  keeps `NAMESPACE` only as an alias of `CoreLibrary::NAMESPACE` for the
  static slot-infra references (`Ui5Registry` auto-expansion,
  `SettingParameterSource`). Rootless test-double modules keep a literal
  `getNamespace()` override.
- **`AbstractUi5Module::getType()` removed.** It returned `ArtifactType::Module`
  but was never consumed (every `getType()` caller operates on a
  `Ui5ArtifactInterface`, never a module) — its removal sharpens the contract: a
  **Module is a container, not an artifact** (it implements neither
  `Ui5ArtifactInterface` nor `HasArtifactIdentity`). The `ArtifactType::Module`
  enum case stays (persisted).
- **UI5 control enums relocated** `LaravelUi5\Core\Ui5\Enums\*` →
  `LaravelUi5\Core\Ui5\Dashboard\Enums\*` — the 22 enums are the dashboard
  control-family vocabulary (tiles, cards, groups, flex/grid layout), so they now
  live under `Dashboard/` with the controls they serve.
- **Moved** existing traits to `Ui5\Conerns` and renamed `HasAssetsTrait` to `HasAssets`.

## [0.9.27] - 2026-05-27

**Invoked-behaviour capability contract — pin the `provide()` / `handle()`
convention and teach the injection recipe.** Surfaced by the Resource API
acceptance review (`docs/meta/specs/core-resource-api-v0.9.md`): the provider /
handler contract was real but type-invisible and the generators steered authors
into the `ExecutableInvoker` method-injection trap. This makes the contract
**documented + runtime-enforced** (a PHP interface cannot type a
container-injected method — the same reason Laravel's `ShouldQueue` is a marker
and never types `handle()`), and makes constructor service-injection the
generated default. **Not breaking** — the named exception extends
`LogicException`, and `app(X::class)` vs `new X()` is behaviour-equivalent for a
zero-dependency provider/handler.

### Added

- **`MissingExecutableMethodException`** (`extends LogicException`) — thrown by
  `ExecutableInvoker` when an invoked target lacks the conventional method
  (`provide()` / `handle()`). Replaces the bare `LogicException` (same message,
  now a first-class, catchable contract failure).
- **The injection recipe, documented on the marker interfaces.**
  `DataProviderInterface` and `ActionHandlerInterface` now carry the contract in
  their docblocks plus `@method array provide()` / `@method array handle()` (so
  IDEs / PHPStan see the method + return type without the interface imposing an
  LSP-rigid signature). The recipe: **services → the constructor** (the artifact
  returns `app(YourProvider::class)`, which also sidesteps the invoker's
  `has()`-only *method*-parameter resolution); **per-request inputs**
  (route-resolved models, `#[Parameter]` / slot values, `FormRequest`, context)
  **→ the method parameters**.

### Changed

- **`ui5:*` generators emit the recipe.** The Resource/Card/Action/Tile/Report
  artifact stubs now return their provider/handler via `app(...::class)` instead
  of `new ...()` — so constructor DI works out of the box. The provider/handler
  stubs (`ResourceProvider`, `CardProvider`, `ActionHandler`, `TileProvider`,
  `ReportProvider`) carry a commented constructor example; the `ReportProvider`
  stub's stale "declare services as method parameters" guidance is corrected to
  services-in-constructor.
- **`ExecutableInvoker`** throws the named exception for a missing method
  (behaviour unchanged otherwise).

## [0.9.26] - 2026-05-27

**Artifact-identity ergonomics + relict sweep — the last two Core 1.0 keystone
decisions, shipped together.** Both are gate-zero items from the 1.0 acceptance
work (`docs/meta/specs/core-1.0-acceptance.md` § 9), batched into one release
since both touch the artifact bases / service provider. **Not breaking** — the
trait is additive (existing hand-written getters override it via late static
binding) and the removed View namespace had no consumer anywhere in the
workspace.

### Added

- **`HasArtifactIdentity` trait** (`src/Ui5/Concerns/`) — const-backed identity
  getters (keystone ②). The four meta attributes stay interface *methods* (the
  intrinsic contract the registry reads polymorphically on every route
  resolution), but their *value* is declared once as a class constant. The 9
  artifact bases (`AbstractUi5App/Library/Card/Tile/Dashboard/DashboardGroup/
  Report/Action/Resource`) now `use` it; a concrete artifact declares only
  `NAMESPACE`/`VERSION`/`TITLE`/`DESCRIPTION` and inherits
  `getNamespace/getVersion/getTitle/getDescription`. **A trait, not a shared
  base** — `AbstractUi5App extends ODataService` already spends PHP's
  single-inheritance slot (`ODataService` was verified to define none of the
  four, so the trait shadows nothing). **`static::` (late static binding)** reads
  the concrete class's constant, which is also what makes it
  **backward-compatible**: a concrete that still hand-writes a getter overrides
  the trait, so existing artifacts are untouched and migration (delete four
  redundant getters) is never forced. **Convention, not enforcement** — a missing
  constant raises a clear `Error: Undefined constant …::NAMESPACE` at the first
  getter call; the reflection-check ceremony PHP itself won't bless isn't worth
  its weight. Rationale: `docs/meta/atoms/ARTIFACT_DEFINITION_MODEL.md` (keystone
  ①, which validated this shape over a `#[Ui5Artifact]` attribute).

### Changed

- **`ui5:*` generator stubs emit the const-only shape.** All 9 stubs
  (`Ui5App/Library/Card/Tile/Dashboard/DashboardGroup/Report/Action/Resource`)
  now declare the four constants and **drop the four literal-returning getters**
  (`NAMESPACE` was already a const; `VERSION`/`TITLE`/`DESCRIPTION` join it).
  Newly scaffolded artifacts inherit the getters from the base.

### Removed

- **Legacy `LaravelUi5\Core\View` namespace** (`Ui5Element` component) and the
  `Blade::component('element', …, 'ui5')` registration in `Ui5CoreServiceProvider`
  (keystone ③ — acceptance gate **V8**: a frozen 1.0 surface carries no dead
  code). The `<x-ui5:element>` component was orphaned by the Dashboard API; a
  workspace-wide grep confirmed zero consumers before removal. The unrelated
  `loadViewsFrom(...)` and the `@IncludeIfSdk` directive directly adjacent are
  untouched.

## [0.9.25] - 2026-05-27

**Reporting API follow-up — ship the control, fix the view path.** A fix-up
release behind 0.9.24: it carries the `<lux:Report>` client control bundle that
0.9.24's notes described but whose build artifact was omitted, and it corrects
report-view resolution so a scaffolded report can find its own Blade.

### Fixed

- **`<lux:Report>` control bundled in `com.laravelui5.core`.** The
  `controls/Report` + `ReportRenderer` + `controls/report/{urlResolver,errors}`
  modules and the `report.error.*` i18n keys are now present in the library
  build (they were missing from the 0.9.24 `resources/` bundle, so `<lux:Report>`
  could not load). No source change to the control — a packaging correction.
- **`AbstractUi5Report::getReportView()` is now a concrete base method.** It
  resolves the document from the host package's conventional layout —
  `<package-root>/resources/ui5/reports/<slug>/report.blade.php`, `<slug>` = the
  last namespace segment (`SluggedSource`), `<package-root>` derived by
  reflection on the module class — exactly mirroring
  `AbstractUi5Card::getManifest()` (and, like it, bypassing the source strategy:
  report blades are server-side PHP). The 0.9.24 `ui5:report` stub hand-rolled a
  relative `__DIR__ . '/../../../resources/…'` path that resolved one directory
  too high (`ui5/resources/…`), so **every scaffolded report 404'd its own
  view**. The stub no longer emits `getReportView()`; generated reports inherit
  the resolution and override only for a non-standard layout.

### Added

- **`MissingReportViewException`** (HTTP 404) — thrown by `getReportView()` when
  no Blade exists at the resolved slug path. Mirrors `MissingCardManifestException`.

 `Report`
(`ArtifactType::Report = 4`) is now a server-rendered HTML artifact,
parameterised by slots, displayed by the host inside the `<lux:Report>`
control (an `<iframe>`). This **replaces** the legacy report infrastructure
wholesale — not a deprecation — and closes the last pre-1.0 Core product item.
It is the sibling of the Dashboard API: same library, **opposite render
contract** — a Dashboard emits a JSON control tree, a Report emits an HTML
document; both share the `#[Slot]` + Parameter-API parameterisation. Spec:
`docs/meta/specs/core-reporting-api-v0.9.md`.

### Changed (breaking)

- **`Ui5ReportInterface` trimmed to two report methods + slot machinery.** Now
  `extends Ui5ArtifactInterface, SlottableInterface`; keeps `getProvider()` +
  `getReportView()`. **Removed** `getSelectionViewPath()` /
  `getSelectionControllerPath()` (selection is host-driven, fed through the
  slot pipeline as query params) and `getActions()` (PDF + follow-up actions
  are host/SDK concerns). `AbstractUi5Report` now defaults `getRequiredSlots()`
  and `getSlotProposals()` to `[]`, mirroring `AbstractUi5Dashboard`.
- **`ReportController` rewritten.** Resolves `getRequiredSlots()` through the
  Parameter API pipeline (Request → [Actor] → Composition → Setting — every
  `#[Slot]` has a default, so a report always renders and host selection merely
  overrides), invokes the provider's `provide(array $slots)` via
  `Container::call` (method-level DI of services preserved; `ExecutableInvoker`
  left untouched per the Parameter-API lock — it has no scalar-bag seat), and
  returns the Blade-rendered HTML `text/html` + `Cache-Control: no-store`.
  Provider signature is now `provide(array $slots, …DI'd deps): array` (was a
  parameterless `provide()` on an `AbstractConfigurable` subclass).
- **`ui5:report` regenerated** to the new shape: a slot-aware report class (with
  a worked `getRequiredSlots()` example), a `provide(array $slots)` provider
  implementing `DataProviderInterface` directly, and a real HTML Blade document
  — no XML/JS selection pair. The command prints a module `#[Slot]` hint. Stubs
  `Report.controller.stub` / `Report.view.stub` removed.

### Added

- **`AbstractManifest::buildReports()` + the `reports` manifest key.** Each
  module's reports surface under `laravel.ui5.reports` as
  `report-id → { url, params }`. Mirrors `buildDashboards()` structurally, but
  the value is an **object**: `url` is the **bare HTML endpoint**
  (`/ui5/report/{ns}@{ver}` — no `/manifest.json` suffix, because a report's
  content IS the HTML document), and `params` lists the report's required slot
  names (the same key the Dashboard served-envelope uses for the same concept).
  The object shape reserves room for a future additive selection-form
  descriptor without a breaking value-type change.

### Removed

- **The `com.laravelui5.report` shell** — `ReportApp`, `ReportModule` (and its
  `Ui5InfrastructureCollector` auto-registration), `resources/report-app/`, and
  the now-dead `report-app` branch in `Ui5SourceStrategyResolver`. The consumer
  embeds `<lux:Report>` in its own app and `ReportController` serves the HTML
  directly — no shell in the loop (the same retirement `DashboardApp` got in
  0.9.20). `LookupTest` infra-module count updated 3 → 2.
- **`ReportResourceController`** + its two `app/com/laravelui5/report/{ns}@{ver}/…`
  routes (served the old XML/JS selection pair). The `report/{ns}@{ver}` route
  is kept — it is the new API's render seat.
- **`MissingReportActionException` / `InvalidReportActionException`** — orphaned
  by the `getActions()` drop.

## [0.9.23] - 2026-05-26 — SemVer credit spent

**Dashboard per-child error isolation + logging on the error path.** A single
malformed or failing dashboard child no longer takes down the whole dashboard,
and the failure stops being silent. This closes the last dashboard item on the
roadmap, split across the two ends of the pipe:

- **Server side (emit walk).** A Tile provider that throws, a Card whose URL is
  invalid, a group template that blows up — previously these bubbled out of
  `DashboardController` as a **whole-endpoint 500**. Now each child's `emit()`
  is isolated by its container emitter: the cause is recorded, the bad node is
  omitted from the tree, and its siblings still render. The controller then
  `report()`s each recorded cause (server logs / Sentry) and stamps a
  **sanitised** entry into a new envelope `errors` array.
- **Client side (control).** The `<lux:Dashboard>` walker (`treeWalker.build`)
  now isolates per-child *construction* failures — the slice-2c
  missing-`TileContent`-wrapper class of bug, which is structurally valid JSON
  the server can't catch but throws in real UI5. The bad node is dropped, its
  siblings render, and both the server `errors` array and the client build
  failures are logged via `sap/base/Log` — so the failure talks to the
  developer in the console instead of a blank dashboard. (Control source lives
  in `ui5-core-lib`; it ships bundled in Core's `resources/ui5/`.)

The root node is deliberately *not* isolated on either side: if the root can't
build there is nothing to render, so it stays the whole-dashboard error path
(500 server-side; the `IllustratedMessage` error state client-side). Sanitised
`errors` reasons are debug-gated — full `Class: message` under `APP_DEBUG`, a
stable generic line in production — so raw exception text never reaches a
customer's browser console.

### Changed (BREAKING — contract)

- **`DashboardElementEmitterInterface` gains `subject(): Ui5ArtifactInterface`.**
  Container emitters need the failing child's identity (namespace + type) to
  record an isolated error. Every emitter already holds exactly one artifact, so
  the four Core emitters implement it trivially; any downstream custom emitter
  (resolved via a rebound transformer) must add it. SemVer credit spent.

### Added

- **`errors` envelope field** on `dashboard/{ns}@{ver}/manifest.json` — a
  (possibly empty) list of `{ namespace, type, reason }` for children the emit
  walk isolated. Additive: `schemaVersion` stays `1.0`; the client validator
  ignores unknown fields, so older clients are unaffected.
- **`DashboardErrorSink`** (`Ui5\Dashboard\DashboardErrorSink`) — the per-request
  collector threaded through `EmitContext`. Sanitises wire reasons at record
  time and retains the raw `Throwable`s for the controller to `report()`
  (never serialised).
- **`EmitContext::$errors`** — the one mutable seat on the otherwise-readonly
  context (the *reference* is fixed). Defaults to a fresh production-safe sink,
  so existing `new EmitContext(...)` call sites need no change.
- **`treeWalker.build` now returns `{ root, errors }`** (was the bare root
  control) and isolates per-child construction throws; `Dashboard.js` consumes
  `result.root` and logs `result.errors` + the server `errors` array via
  `sap/base/Log`. *(ui5-core-lib; bundled into `resources/ui5/`.)*

## [0.9.22] - 2026-05-26 — SemVer credit spent

**Integration-card i18n (card-relative bundle).** `sap.ui.integration` cards
are sandboxed — a card resolves `{i18n>…}` against its **own** ResourceBundle
declared in `sap.app/i18n` and fetched relative to the card manifest URL, **not**
the host component's i18n model (the assumption Dashboard v0.9 §D12 made, valid
for the native control tree but not for Cards). Core now serves that bundle and
injects the pointer, so card copy can finally be translated. Each card also
becomes a self-contained folder — a canonical card package: its manifest beside
an optional `i18n/` directory.

### Changed (BREAKING — convention)

- **Card manifest path moved into a per-card folder.**
  `AbstractUi5Card::getManifest()` now reads
  `resources/ui5/cards/<slug>/manifest.json.blade.php` (was the flat
  `resources/ui5/cards/<slug>.blade.php`). Every existing card must move its
  blade into a `<slug>/` folder and rename it `manifest.json.blade.php`. The
  `.blade.php` suffix keeps editors treating the template as Blade rather than
  invalid JSON; the served resource is still `manifest.json`. Consumers that
  override `getManifest()` for a non-standard layout are unaffected.

### Added

- **Card i18n bundle endpoint** — `card/{ns}@{ver}/i18n/{file}` (`CardI18nController`,
  new route in `routes/ui5.php`) serves a card's `.properties` files
  (`text/plain; charset=utf-8`), card-relative to the manifest. `{file}` is
  constrained to `i18n[_<locale>].properties` and the resolved path is confirmed
  inside the bundle directory (traversal guard). Hard-cacheable (`@{ver}`-keyed),
  unlike the per-request manifest.
- **`sap.app/i18n` injection on the card endpoint** — when a card ships an
  `i18n/` bundle, `CardController` injects
  `{ bundleUrl: "i18n/i18n.properties", supportedLocales, fallbackLocale: "" }`
  into `sap.app`, with `supportedLocales` derived from the `.properties` files on
  disk. Cards **without** a bundle pass through byte-for-byte unchanged — no
  decode/encode round-trip, no behaviour change. (Unlike `ManifestController`, no
  empty-array→object normalisation is applied: card manifests favour empty arrays
  like `"actionsStrip": []`, which must survive; the only lossy case is a literal
  empty object `{}`, which card manifests don't carry.)
- **`Controllers\Concerns\ResolvesCardI18nBundle`** — internal trait, shared by
  both controllers, deriving the bundle directory (`cards/<slug>/i18n`) from the
  card's module + namespace. Kept off `Ui5CardInterface` deliberately: the
  convention is derivable from the artifact's public surface, so it earns no
  contract method (additive later if a non-standard layout ever needs it).
- **`MissingCardI18nBundleException`** (404).

### Blessed binding syntax

- Translate card strings with the **`{i18n>KEY}`** model-binding form. The
  `sap.ui.integration` double-brace `{{KEY}}` translation form is **not** used —
  it collides with Blade's echo syntax in the manifest template. (`@{{KEY}}`
  remains an escape hatch if static pre-binding resolution is ever needed.)

### Generator

- **`ui5:card`** scaffolds the new layout: `cards/<slug>/manifest.json.blade.php`
  (now with a `sap.app` block + `{i18n>…}` header bindings) plus
  `cards/<slug>/i18n/i18n.properties`. New stub `resources/stubs/CardI18n.stub`;
  `CardManifest.stub` rewritten.

## [0.9.21] - 2026-05-26

**Infrastructure manifest contributions** — a new `laravel.ui5/infra` node lets
infrastructure modules publish static, cross-cutting facts that every app's
client needs regardless of which module it loaded. Until now the only
contribution surface was per-module (`contributeFragment()` → `routes`/`vendor`),
so a platform fact like the logout-route handle had to be smuggled into one
module's `routes` and re-declared by the next module that needed it. The same
gap was about to bite the Reporting API, which needs the slot catalog
client-side. `infra` closes both: a fact is declared once, by its owner, and
injected identically into every module's manifest. Spec:
`docs/meta/specs/core-infrastructure-contributions-v0.9.md`.

### Added

- **`LaravelUi5ManifestKeys::INFRA = 'infra'`** — the new top-level manifest
  node, nested (modules as sub-keys: `infra.core`, `infra.auth`, …). Added to
  `LaravelUi5ManifestKeys::all()`.
- **`Ui5InfrastructureContributorInterface`** (`src/Ui5/Capabilities/`) — opt-in
  capability (`getInfrastructureKey(): string` + `contribute(Ui5RegistryInterface): array`),
  checked by `AbstractManifest` via `instanceof`, mirroring
  `Ui5ShellFragmentInterface`. The interface — not the `Ui5Infrastructure`
  marker — is the gate; contributions MUST be static and user-invariant (the
  node rides into a cacheable, cross-user manifest). The node is **not**
  aggregatable via `contributeFragment()`; the contributor interface is the
  only door in. Duplicate `getInfrastructureKey()` across contributors raises
  `LogicException` at build time.
- **`AbstractManifest::buildInfra()`** — loops `Ui5RegistryInterface::modules()`,
  folds in every contributor's `contribute()`. No constructor change (uses the
  already-injected registry); no `Ui5RegistryInterface` additions.
- **`CoreModule` is the first contributor** (`infra.core`) — publishes the slot
  catalog as `infra.core.slots` (per-slot `type`/`default`/`editable`/`note`,
  read from `getAllSlots()`), so report clients can describe slot-parameterised
  selection inputs without each module re-declaring them.

## [0.9.20] - 2026-05-26

**`ui5:group` generator** — DashboardGroups were the one artifact without a
scaffold (every other type has `ui5:app`/`card`/`tile`/`action`/`resource`/
`dashboard`); consumers hand-copied them from a sibling. Closes the
ROADMAP "Generator: `ui5:dashboard-group`" item.

### Added

- **`GenerateUi5DashboardGroup` command** (`ui5:group {Module}/{Group}`) —
  scaffolds a `Ui5DashboardGroupInterface` impl extending
  `AbstractUi5DashboardGroup`, placed in `ui5/{Module}/src/Dashboards/`
  (beside the dashboard, same PHP namespace as `ui5:dashboard` emits).
  Class is suffixed `Group` (e.g. `Portal/LicenseAndAction` →
  `LicenseAndActionGroup`); UI5 namespace is `{module}.groups.{kebab}`,
  parallel to `ui5:tile`'s `{module}.tiles.{key}`. The stub ships only the
  identity getters + an empty `getChildNamespaces()`, with comments on
  adding children and overriding the `getPanel()`/`getGridContainer()` DTO
  factories for UX. New stub `resources/stubs/Ui5DashboardGroup.stub`.

### Changed

- **`ui5:app` and `ui5:lib` build by default.** The opt-in `--auto-build` flag
  is replaced by build-always with a `--no-build` opt-out — matching the
  intended workflow (develop the UI5 sub-project in parallel, then `--refresh`
  at deploy to rebuild and re-import `dist/`). **Breaking** (Core-only,
  in-house): drop `--auto-build` from invocations; pass `--no-build` to skip.
  For `ui5:app`, `--refresh` deliberately keeps rewriting the App POPO so a
  newly added `sap.*` library re-syncs into the PHP source (the
  identity-preserving `--refresh` fix below holds namespace + version steady).

### Removed

- **`DashboardModule` + `DashboardApp` — the `com.laravelui5.dashboard` shell
  app — and `resources/dashboard-app/`.** Dead since the Dashboard API (0.9.15+)
  resolves dashboards from the registry via `DashboardController`
  (`dashboard/{ns}@{ver}/manifest.json`) + the `<lux:Dashboard>` control. Also
  dropped the now-unreachable `dashboard-app` branch in
  `Ui5SourceStrategyResolver` and the `->add(DashboardModule::class)`
  infrastructure registration. **Kept:** `DashboardController`, its route, and
  the control assets in `resources/ui5/controls/dashboard/`. (`ReportModule` /
  `ReportApp` follow with the Minimal Reporting API.)

### Fixed

- **`ui5:app --create` baked the literal `${version}` token** into
  `{Name}App::getVersion()`. The version is read from the source
  `webapp/manifest.json`, where `applicationVersion.version` carries the
  unresolved `generator-ui5-ts-app` build token; the generated URL
  (`/ui5/app/{ns}@${version}/…`) never resolved and every consumer
  hand-fixed it. `--create` now writes the SAP-convention `1.0.0` — the
  artifact `@{ver}` is a deliberate identity coordinate (shared with the
  dev vhost proxy + asset paths), not a value to derive. Atom
  `docs/meta/atoms/ARTIFACT_VERSION_IDENTITY.md`.
- **`ui5:app --refresh` clobbered a migrated PHP namespace** (and would
  have reset a hand-bumped version). The always-rewritten App class took
  its namespace from the `--php-ns-prefix` option default, silently
  reverting e.g. a `Pragmatiqu\…` → `LaravelUi5\…` migration. `--refresh`
  now reads **both** the PHP namespace and the version back from the
  existing App class and re-emits them unchanged (source-derived fields —
  title, bootstrap, deps — still refresh); it **aborts** rather than
  clobber if either can't be parsed.
- **`ui5:lib --refresh` clobbered the PHP namespace and rewrote the Module
  class** (wiping any hand-registered cards/tiles/dashboards). Same root cause
  as the `ui5:app` namespace bug: `$phpNamespace` came from the
  `--php-ns-prefix` default, applied unconditionally to both the Library and
  Module classes (no create-gate). `--refresh` now reads the namespace back
  from the existing Library class (aborting if unreadable) for the Library
  rewrite + the registration key, and the **Module class is written at
  `--create` only**. The Library class still rewrites on refresh to re-sync
  source-derived metadata; its version stays **derived** from the built
  `.library` (libraries are npm-versioned — `package.json` is authoritative),
  a deliberate divergence from the app's hard-coded identity version. The
  `${version}` token bug never affected `ui5:lib` (it reads the build-resolved
  `dist/.../.library`, not the templated source).

## [0.9.19] - 2026-05-24 — SemVer credit spent

**Dashboard visibility veto** — a pre-data, server-side prune of artifacts
the actor shouldn't see, composed from registered vetoers and leaving the
transformer's job (artifact → emitter tree) otherwise intact. The Core
default is a **no-op**: with nothing tagged, the chain is empty and every
artifact renders, exactly as before. First real consumer is the portal's
`PortalLifecycleVetoer` (tier/state gating, lands with portal slice 2f);
the SDK's `AccessVetoer` joins the same chain when SDK access work lands —
no transformer subclass, ever. Atom `DASHBOARD_VETOER.md`; spec
`core-dashboard-api-v1.0.md` § "Visibility veto".

### Added

- **`Disposition` enum** (`Ui5/Dashboard/`) — `Show` / `Hide` / `Lock`.
  `Lock` is **reserved** (visible-but-read-only, not yet enforced — the
  prune treats it as `Show`); declared now so the contract is stable when
  read-only rendering lands.
- **`DashboardVetoerInterface`** (`Ui5/Capabilities/`) —
  `dispose(Ui5ArtifactInterface, Ui5ContextInterface): Disposition`. A
  registered participant voting on one artifact; knows neither the others
  nor the tree.
- **`DashboardVetoChain`** (`Ui5/Dashboard/`) — folds the registered
  vetoers by most-restrictive-wins (any `Hide` ⇒ Hide; else any `Lock` ⇒
  Lock; else Show). Monotonic, order-independent, empty ⇒ Show.
- **Tag-based registration.** `DefaultDashboardTransformer` consults a
  `DashboardVetoChain` built from every binding tagged
  `DashboardVetoerInterface::class`, pruning `Hide` artifacts per group
  and per child before their emitters are built. Contributors register a
  vetoer via `$this->app->tag([MyVetoer::class], DashboardVetoerInterface::class)`.

### Changed (breaking — no in-house consumer affected)

- `DefaultDashboardTransformer::__construct` gains a second arg,
  `DashboardVetoChain $vetoChain = new DashboardVetoChain()` (empty
  default → no-op; existing `new DefaultDashboardTransformer($registry)`
  calls keep working).
- The `protected buildDashboardEmitter()` / `buildGroupEmitter()` now take
  `Ui5ContextInterface $context` (needed to consult vetoers mid-walk). This
  only affects a subclass that overrides them; there are **no such
  subclasses in-house** (the SDK uses a vetoer, not a transformer — the
  `AuthorizedDashboardTransformer` sketch from v0.9 D7 was never built).

## [0.9.18] - 2026-05-24 — SemVer credit spent

**Bugfix (tiles never rendered against real UI5) + the breaking structural
change that fixes it.** Core modelled a tile's content as a `Payload`
(`NumericContent` / …) placed *directly* in `sap.m.GenericTile.tileContent`.
But that aggregation is typed `sap.m.TileContent` — the content control
belongs one level deeper, in a `TileContent` wrapper's `content`
aggregation. So `new GenericTile({tileContent: [<NumericContent>]})`
throws an invalid-aggregation error in real UI5; the `ui5-core-lib`
Dashboard control caught it and showed its error state. The flaw shipped
in the v0.9 tile work and stayed invisible because the Tier-1 walker tests
stub their controls (a fake ctor accepts anything) and Tier-3 was
deferred — the portal's first browser-rendered tile (ui5-portal slice 2c)
surfaced it.

### Added

- **`sap.m.TileContent` DTO** (`Ui5/Dashboard/Controls/Sap/M/TileContent.php`)
  — the required wrapper between a tile and its `Payload`. Carries the
  `content` aggregation (one `Payload`) plus the footer/unit affordances
  that only exist at this level: `footer`, `footerColor` (`ValueColor`),
  `unit`, `frameType`, `priority`, `priorityText`, `state`, `disabled`.
  All enum-typed seats use existing Core enums.
- `ui5-core-lib`: `sap.m.TileContent` added to the Dashboard control's
  vocabulary (the walker already recurses `TileContent.content`).

### Changed (breaking)

- **`GenericTile.tileContent` is now `TileContent[]`, not `Payload[]`.**
  A provider wraps each content control:
  `new GenericTile(tileContent: [new TileContent(content: new NumericContent(...))])`.
  The four `Payload` content DTOs and the `Payload` marker are unchanged —
  they're now correctly understood as `TileContent.content`, not tile
  content directly.
- **`GenericTile::withTileContent()` removed.** It was already vestigial
  after 0.9.17 (the provider builds the whole tile); it had no framework
  caller and its `new self` downcast-on-`ActionTile` wart is now moot.
  **Migration:** wrap every `tileContent` element in a `TileContent`; drop
  any `withTileContent()` call (pass `tileContent:` to the constructor).

### Note — the Tier-3 gap this exposes

`ui5-core-lib`'s Karma harness serves only the lib's own resources, not
full OpenUI5, so its walker tests *must* stub `sap.m` controls — they
structurally cannot catch a UI5 aggregation-type mismatch. A real-control
guard belongs in Tier 3 (the consuming app). Logged for the dashboard
Tier-3 sign-off; see `docs/meta/specs/core-dashboard-api-v1.0.md`.

## [0.9.17] - 2026-05-24 — SemVer credit spent

The Tile sub-API reaches feature-complete and its contract settles — the
**Dashboard API surface graduates to v1.0** (the *package* stays on
`0.9.x`; the OData-backed contracts in PLAN.md are still moving and own
the road to a `1.0.0` package). A tile's provider now returns the **whole
tile** (shell *and* content), the tile vocabulary covers all three SAP
tile roots, and the `Payload*` naming that no longer fit is retired.

**Why the reshape:** the previous split — a static `getGenericTile()`
template on the artifact + a `PayloadProviderInterface` that filled only
`tileContent` — could not express a *data-driven shell* (a `subheader`
carrying "last used 2 days ago", a `valueColor` reflecting health),
because only the executor has the data and it could only touch the
content. Making the executor return the whole tile dissolves that seam.

> **Pinned to OpenUI5 1.136.x.** The tile DTOs (`GenericTile` /
> `ActionTile` / `SlideTile`) and every property seat are modelled
> against OpenUI5 **1.136** — the version `ui5-portal` + the bundled
> `ui5-core-lib` ship against (`sap.m.ActionTile` only exists since
> 1.122). Verify property changes against the 1.136 API reference, not
> training recall. See `docs/meta/specs/core-dashboard-api-v1.0.md` and
> the box on `LaravelUi5\Core\Ui5\Dashboard\Tile`.

### Added

- **`Tile` marker interface** (`Ui5/Dashboard/Tile.php`) — narrows
  `DashboardElement` to the tile *roots*, paralleling `Payload` (which
  marks tile *content*). `GenericTile`, `ActionTile`, and `SlideTile`
  implement it. The tile provider returns `Tile`, not a closed
  `GenericTile|ActionTile|SlideTile` union — a future tile type only has
  to `implements Tile`.
- **`sap.m.ActionTile` DTO** — extends `GenericTile` (faithful to SAP,
  since 1.122) and adds the six ActionTile seats (`enableDynamicHeight`,
  `enableIconFrame`, `priority`, `priorityText`, `badgeIcon`,
  `badgeValueState`), all emit-when-set. `priority` is the existing
  `Priority` enum; `badgeValueState` is the new `ValueState` enum;
  `badgeIcon` stays a string (it is a `sap.ui.core.URI` icon URI, not an
  enumerated value).
- **`ValueState` enum** (`sap.ui.core.ValueState`: `Error` / `Information`
  / `None` / `Success` / `Warning`) — backs `ActionTile::$badgeValueState`,
  verified against the 1.136 reference.
- **`sap.m.SlideTile` DTO** — a `Tile` container with a `tiles`
  aggregation of `GenericTile`s plus `displayTime` / `transitionTime` /
  `scope` / `sizeBehavior` / `width` / `height` seats (since 1.34).
- Tile-vocabulary serialization tests covering the marker + ActionTile +
  SlideTile (259 tests green).

### Changed (breaking)

- **`PayloadProviderInterface` → `TileProviderInterface`; `getPayload()`
  → `getTile()` returning `Tile`.** The provider now builds the entire
  tile (shell + content), not just the `Payload` for `tileContent`:
  `getTile(array $boundParams, Ui5ContextInterface $context): Tile`.
- **`Ui5TileInterface::getPayloadProvider()` → `getTileProvider()`**, and
  **`getGenericTile()` is removed** from the interface and
  `AbstractUi5Tile`. A tile artifact is now pure metadata (identity +
  slots) plus a provider handle; it is no longer a DTO factory for its
  own shell.
- **`GenericTileEmitter` → `TileEmitter`** — collapses to
  `getTileProvider()->getTile($bound, $context)` and returns the result
  verbatim (no template + `tileContent`-merge step).
- **`GenericTile` is no longer `final`** (so `ActionTile` can extend it)
  and now `implements Tile`. Its `withTileContent()` survives as a
  convenience copy-with but is no longer a framework seat — the provider
  passes `tileContent:` to the constructor.
- `Payload` (the content marker) and the four `TileContent` subtypes
  (`NumericContent` / `ImageContent` / `NewsContent` / `FeedContent`) are
  **unchanged**.
  **Migration:** rename `implements PayloadProviderInterface` →
  `TileProviderInterface`; rename the method `getPayload(array $b)` →
  `getTile(array $b, Ui5ContextInterface $context)` and return a `Tile`
  (wrap your `NumericContent` in a `new GenericTile(tileContent: […])`
  and move any `getGenericTile()` shell properties into it); rename the
  artifact's `getPayloadProvider()` → `getTileProvider()` and delete its
  `getGenericTile()`. The `ui5:tile` generator now scaffolds a
  `{Name}TileProvider` returning a full `GenericTile`.

## [0.9.16] - 2026-05-24 — SemVer credit spent

A Tile's payload provider now receives the request's runtime context, so
a tile can read the actor (and any other identity-bearing scope) the same
way a Card's `provide()` already does — without abusing a scalar slot to
carry rich state. Cards get their context by signature injection at the
card endpoint (the `ExecutableInvoker` resolves the concrete type); a Tile
has no endpoint — its payload is computed inline during dashboard
composition — so the context is threaded explicitly through the emitter
chain instead.

### Added

- **`EmitContext::$context` — the request's runtime scope.** `EmitContext`
  gains a fourth constructor seat, a `Ui5ContextInterface`, alongside
  `pipeline` / `request` / `registry`. `DashboardController` passes the
  same context it was handed, and the container emitters thread it
  unchanged to the leaves — exactly the "add cross-cutting state here once
  and every emitter sees it" extension the class docblock anticipated.
  Whatever the active layer bound flows through: Core-only it's the
  technical context; with the SDK installed it's the identity-bearing
  `SdkContext`, which a tile narrows with `instanceof` to read the actor.

### Changed (breaking)

- **`PayloadProviderInterface::getPayload()` takes the context.** The
  signature is now
  `getPayload(array $boundParams, Ui5ContextInterface $context): Payload`
  (was `getPayload(array $boundParams): Payload`). `GenericTileEmitter`
  passes `EmitContext::$context` as the second argument. The argument is
  typed to the `Ui5ContextInterface` contract — Core stays ignorant of the
  SDK's `SdkContext`; consumers that need the actor narrow it themselves.
  **Migration:** add the `Ui5ContextInterface $context` parameter to every
  `getPayload()` implementation (a no-op for providers that don't use it).
  Code constructing `EmitContext` directly (test harnesses, custom
  controllers) now passes a fourth `Ui5ContextInterface` argument;
  consumers that go through `DashboardController` are unaffected.

## [0.9.15] - 2026-05-24 — SemVer credit spent

The dashboard control DTOs gain a content-rich property surface, and the
`class` seat is generalised across every control via a shared
template-method merge on `AbstractControl`. Enrichment properties are
**emit-when-set**: a plain control stays as lean on the wire as before; a
rich one carries only the seats it sets, and an absent property falls
through to UI5's own default at `applySettings` time. The vocabulary of
nine control classes is unchanged, so the `ui5-core-lib` Dashboard walker
needs no change (it passes managed settings through verbatim) — this is a
Core-only release.

### Added

- **`AbstractControl::optionalProperties()` — emit-when-set merge.** A new
  third seat alongside `ui5Class()` / `properties()`. `jsonSerialize()`
  is now a final template method that appends each optional property only
  when non-null and normalises a `BackedEnum` value to its backing string.
  This retires the hand-written `if ($this->x !== null) { … }` ladders
  (and per-property `->value` calls) that had begun to repeat across
  `VBox`, `Card`, `GenericTile`, and `GridContainerItemLayoutData`.
- **`class` seat on every visual control DTO.** `Panel`, `GridContainer`,
  `Card`, `GenericTile`, `NumericContent`, `NewsContent`, `ImageContent`,
  and `FeedContent` join `VBox` in accepting an optional `class`
  (`Margin|ContentPadding|string|null`) — a single spacing enum case or a
  free CSS string (combine via the string form). Applied client-side via
  `addStyleClass()`; omitted from the wire when null. (`GridContainerItemLayoutData`
  is layout metadata, not a styled control, so it gets none.)
- **Spacing enums `Margin` + `ContentPadding`.** The full set of UI5
  predefined CSS margin classes (four sizes × the RTL-aware edge matrix,
  plus responsive / reset / negative / force-width-auto) and the
  container content-padding family — so the class names never have to be
  looked up by hand again.
- **`LoadState` + `PanelAccessibleRole` enums.** `LoadState`
  (Loaded/Loading/Failed/Disabled) backs the tile/numeric-content load
  status; `PanelAccessibleRole` (Form/Region/Complementary) backs the
  panel ARIA role.
- **`Panel` enrichment.** Emit-when-set `accessibleRole`,
  `backgroundDesign`, `expandable`, `expandAnimation`, `expanded`,
  `height`, `stickyHeader`, `width` (`headerText` + `content` stay
  always-carried).
- **`GenericTile` enrichment.** Emit-when-set `subheader`, `mode`,
  `scope`, `sizeBehavior`, `wrappingType`, `state`, `failedText`,
  `systemInfo`, `additionalTooltip`, `imageDescription`, `valueColor`,
  `backgroundColor`, `backgroundImage`, `headerImage`, `tileIcon`,
  `tileBadge`, `width`, `url`, `enableNavigationButton`,
  `navigationButtonText`, `pressEnabled` (`header` + `frameType` +
  `tileContent` stay always-carried). Verified against OpenUI5 1.136.
- **`NumericContent` enrichment.** Emit-when-set `valueColor`, `state`,
  `icon`, `iconDescription`, `truncateValueTo`, `width`, `withMargin`,
  `adaptiveFontSize`, `animateTextChange`, `formatterValue`,
  `nullifyValue` (`value` stays always-carried).
- **`FeedContent` `valueColor`.** Emit-when-set semantic colour for the
  feed value.

### Changed (breaking)

- **`GenericTile::$frameType` is now a `FrameType` enum** (was a `string`
  defaulting to `'OneByOne'`). Wire output is unchanged (`FrameType::OneByOne`
  → `'OneByOne'`). **Migration:** pass `FrameType::…` instead of a string
  literal.
- **`NumericContent::$indicator` is now a `?DeviationIndicator` enum** (was
  a `string` defaulting to `'None'`). **Migration:** pass
  `DeviationIndicator::…` instead of a string literal.
- **`NumericContent` default wire shape is leaner.** `scale` and
  `indicator` are now emit-when-set rather than always-carried, so a
  `NumericContent` with only a `value` serialises to `{"value": …}`
  alone (previously also `"scale": ""` and `"indicator": "None"`).
  Behaviourally identical on the client — the omitted properties resolve
  to UI5's own defaults — but consumers asserting on the exact JSON must
  drop the two keys.

## [0.9.14] - 2026-05-24

The Dashboard control gains a plain `action` event so consumers can act
on card/tile actions — the un-deferral of the action surface cut from
the v0.9 control. Core stays event-only; intents remain an SDK concern.
See `docs/meta/specs/core-dashboard-actions-v0.9.md`.

### Added

- **Dashboard control `action` event** (`ui5-core-lib`,
  `com.laravelui5.core.controls.Dashboard`). One event re-emitting child
  actions: a card's native `Custom` action, or an actionable tile's
  press. `getSource()` is the Dashboard control (idiomatic UI5, cf.
  `sap.m.List#itemPress`); parameters are `{ child, type, parameters }` —
  `child` is the originating `Card`/`GenericTile`, `parameters` is the
  verbatim intent. Consumers attach one handler and switch on
  `parameters.method`. Raw card `Navigation` actions self-navigate and
  do **not** surface. The walker stashes the press-intent as custom data;
  the control wires `attachAction`/`attachPress` post-build (walker stays
  materialization-only). **No intent dispatch in Core** — the SDK intent
  system is a future event-consumer, never a Core import.
- **`GenericTile` DTO `parameters` seat.** A tile is actionable only via
  a press handler (`sap.m.GenericTile` never self-navigates), so a tile
  carries its press-intent in a new optional `parameters` array (e.g.
  `['method' => 'open-tokens']`), emitted on the wire only when set
  (omit-when-null, like `layoutData`). Not a real `GenericTile` property
  — the control extracts it (like `class`) and replays it as the
  `action` event's `parameters` on press.

## [0.9.13] - 2026-05-24 — SemVer credit spent

The `ui5:*` generators give every scaffolded artifact a referenceable
namespace constant, and dashboard groups now compose their children **by
namespace** (resolved against the registry) instead of by instantiating
them — one artifact identity, validated at composition.

### Added

- **Artifact stubs declare `public const NAMESPACE`.** All nine `ui5:*`
  generator stubs (`Ui5App`, `Ui5AppSelfContained`, `Ui5Library`,
  `Ui5Card`, `Ui5Tile`, `Ui5Report`, `Ui5Action`, `Ui5Resource`,
  `Ui5Dashboard`) now emit `public const NAMESPACE = '<ns>';` with
  `getNamespace()` returning `self::NAMESPACE`. This lets one artifact
  reference another's identity without instantiating it (e.g. a
  dashboard group declaring its children by `SomeCard::NAMESPACE`) —
  the prerequisite for namespace-based dashboard composition. Purely
  additive: generated code only, single source of truth, no runtime or
  contract change. Existing hand-written artifacts adopt the const when
  next touched.

### Changed (breaking)

- **Dashboard groups declare children by namespace.**
  `Ui5DashboardGroupInterface::getChildren(): Ui5ArtifactInterface[]` is
  replaced by `getChildNamespaces(): string[]` — a group returns each
  child's `::NAMESPACE` rather than `new`-ing up an instance.
  `DefaultDashboardTransformer` resolves each namespace against the
  registry to the single registered instance (it now takes a
  `Ui5RegistryInterface` constructor dependency), eliminating the second
  card/tile instance groups used to create. A namespace the registry
  can't resolve throws the new `UnregisteredDashboardChildException` at
  composition time — so "register the child on its module
  (`getCards()`/`getTiles()`)" is now a checked invariant instead of a
  silent 404 at manifest fetch.
  **Migration:** change each group's `getChildren()` (returning artifact
  instances) to `getChildNamespaces()` (returning `[SomeCard::NAMESPACE,
  …]`), and make sure every child is registered on its module. Code that
  constructs `DefaultDashboardTransformer` directly now passes a
  `Ui5RegistryInterface` (container-resolved consumers and subclasses
  without their own constructor are unaffected).

## [0.9.12] - 2026-05-24

Namespace validators now accept underscores, so the `ui5:*` generators'
own output is a valid artifact identity. The `sap.m.VBox` control DTO
gains the full FlexBox layout surface.

### Added

- **`VBox` control DTO — full FlexBox layout surface.** `sap.m.VBox`
  (the Dashboard root container, and a `getVBox()` seat on dashboard
  artifacts) now carries the complete `sap.m.FlexBox` property set:
  `class`, `alignContent`, `alignItems`, `backgroundDesign`, `columnGap`,
  `direction`, `displayInline`, `fitContainer`, `gap`, `height`,
  `justifyContent`, `renderType`, `rowGap`, `width`, `wrap`. Seven new
  string-backed enums back the typed properties — `BackgroundDesign`,
  `FlexDirection`, `FlexAlignItems`, `FlexJustifyContent`,
  `FlexAlignContent`, `FlexWrap`, `FlexRendertype` — with values and
  defaults mirroring the SAP API (VBox's `direction` defaults to
  `Column`). The FlexBox layout properties are real `sap.m.FlexBox`
  metadata and are emitted unconditionally (same convention as the
  `Card` DTO's scalars). `class` is the one optional seat — `?string`,
  default `null`, emitted only when set, exactly as `Card` emits
  `layoutData` only when non-null — because it is not a managed UI5
  property but a CSS class list applied client-side via `addStyleClass()`.
- **`ui5-core-lib` Dashboard control applies VBox layout properties.**
  The bundled Dashboard walker now reads the `sap.m.VBox` node's FlexBox
  properties (alignment, gaps, wrapping, sizing, background, render type)
  onto the control and `addStyleClass()`es `class`, so server-set
  container layout actually renders. Shipped in the `ui5-core-lib` dist
  bundled into Core for this release.

### Fixed

- **Underscore permitted in Card + Dashboard namespaces.** The `ui5:card`
  and `ui5:dashboard` generators slug names with `Str::snake()`, producing
  snake_case segments (e.g. `io.pragmatiqu.portal.cards.next_best_action`).
  The namespace regexes in `CardEmitter` (manifest-URL composition) and
  `DashboardController` (envelope guard) were `^[a-z0-9.-]+$`, which
  rejected those — the framework refused identities its own scaffolder
  emits, surfacing as `InvalidCardManifestUrlException` /
  `InvalidDashboardNamespaceException` at dashboard render. Both regexes
  (and their exception messages) widen to `^[a-z0-9._-]+$`; `_` is a
  URL-unreserved character (RFC 3986), so the composed manifest URL stays
  well-formed. Strictly widening — no previously-valid namespace changes
  meaning.

## [0.9.11] - 2026-05-23 — SemVer credit spent

Synthetic slot settings are now owned by the Core library artifact instead
of a reserved sentinel namespace, so that every settings-namespace is an
artifact-namespace. This closes the seam that made persistence layers (the
SDK's `SettingsWorker`) abort on the `slot` namespace, which has no backing
artifact.

### Added

- **`CoreModule::NAMESPACE` constant** (`'com.laravelui5.core'`). Single
  source of truth for the Core infrastructure module's name and its library
  artifact's namespace — the two the `Ui5ModuleInterface` contract requires
  to be identical.

### Changed (breaking)

- **Synthetic slot settings relocated.** `#[Slot]` auto-expansion (D14) now
  writes the `slot.{name}` synthetic Settings under the Core library
  artifact namespace (`CoreModule::NAMESPACE` = `com.laravelui5.core`)
  instead of the reserved `slot` sentinel. `Ui5RegistryInterface::settings('slot')`
  now returns `[]`; the same entries are reachable via
  `settings(CoreModule::NAMESPACE)`, keyed `slot.{name}` exactly as before
  and still flagged `synthetic: true`. Consumers that walked
  `registry->settings()` and assumed every namespace maps to a registered
  artifact (canonically the SDK's `SettingsWorker`, whose `sdk_settings.artifact_id`
  is a NOT NULL FK) now resolve a real artifact for slot defaults — no
  sentinel special-casing required.

### Changed

- **`SettingParameterSource` reads from `CoreModule::NAMESPACE`.** The
  runtime slot-default fallback now sources the catalog from the Core
  library namespace; the `slot.{name}` key filtering and sentinel
  (`@today`, …) post-processing are unchanged, so resolved values are
  identical.
- **`CoreLibrary::getNamespace()` derives from its module.** Returns
  `getModule()->getName()` instead of a duplicated literal, enforcing the
  module-name ≡ library-namespace invariant by construction so the two can
  no longer drift.

### Note for SDK / persistence consumers

After upgrading, slot defaults arrive under `com.laravelui5.core` (a real,
`ArtifactsWorker`-persisted artifact) rather than the `slot` sentinel. A
consumer that special-cased the sentinel can drop that handling; the
`slot` namespace simply no longer appears.

## [0.9.10] - 2026-05-23

Grid placement for dashboard items. `SettingResolver` no longer 
requires `AbstractConfigurable` for targets without `#[Setting]` 
attributes. Additive only; no contract breakage.

### Added

- **`GridContainerItemLayoutData` DTO**
  (`\LaravelUi5\Core\Ui5\Dashboard\Controls\Sap\F\GridContainerItemLayoutData`).
  Typed counterpart of `sap.f.GridContainerItemLayoutData` — the
  layout-data object attached to items inside a `sap.f.GridContainer`
  to dictate column/row span. Constructor: `columns` (default 2),
  `rows` (default 1), nullable `minRows` / `maxRows`. `null`-valued
  optionals are omitted from the wire (so UI5's own defaults apply
  when the consumer doesn't opine).
- **`layoutData` seat on `Card` and `GenericTile`.** Both DTOs gain a
  nullable `?GridContainerItemLayoutData $layoutData = null`
  constructor param. When set, it serializes as a nested control spec
  (`"layoutData": {"sap.f.GridContainerItemLayoutData": {...}}`) that
  the ui5-core-lib walker materialises and UI5's `ManagedObject`
  settings handler picks up via the standard `setLayoutData()` path.
  When null, the key is omitted (existing wire shape preserved —
  pre-0.9.11 consumers see no change). Framework-injection seats
  (`Card::withManifest()`, `GenericTile::withTileContent()`) preserve
  consumer-set `layoutData` across rebuild.

### Fixed

- **`SettingResolver::resolve()` threw `InvalidSettingException` on
  any non-`AbstractConfigurable` target.** The `instanceof
  AbstractConfigurable` check ran *before* the "no `#[Setting]`
  attributes → return early" branch, so a provider with zero declared
  settings still had to extend the base class — even though
  `injectSettings()` was never going to be called. The resolver now
  reflects-for-attributes first, returns early when there are none,
  and only enforces the base-class requirement when settings are
  actually about to be injected. Existing behavior preserved for any
  target that *does* declare `#[Setting]`s without extending
  `AbstractConfigurable` (still throws). This unblocks `DataProvider`
  classes that use constructor DI for their own dependencies (host
  services) and don't need the framework's setting machinery.

### Note for ui5-core-lib consumers

The wire-side `sap.f.GridContainerItemLayoutData` control spec needs
a matching entry in the client's dashboard-walker vocabulary. Ships
in the next ui5-core-lib release alongside this Core patch — same
Satis cycle.

## [0.9.9] - 2026-05-23 — SemVer credit spent

Card-pipeline fixes — URL composition + manifest-blade resolution.

### Fixed

- **`CardEmitter` emitted dotted namespaces in card-endpoint URLs.**
  The hand-rolled `sprintf('/.../card/%s@%s/...', $namespace, $version)`
  put the dotted form (`io.example.cards.x`) into the wire, but Core's
  canonical URL convention is the slashed form (`io/example/cards/x`)
  per `Ui5Registry::namespaceToPath()`, and the route declaration
  already accepts only the slashed shape (the catch-all `'namespace' =>
  '.+'` constraint matches both, but the route group's overall behavior
  is designed around slashes). The dotted URLs 404'd as soon as a real
  router lookup was attempted from a card on a real dashboard. The
  emitter now delegates to `Ui5Registry::resolve()`, which composes the
  URL from `namespaceToPath()` + the artifact's `routePrefix` + version
  — single source of truth.
- **`AbstractUi5Card::getManifest()` resolved blade paths via the
  source strategy.** That works for the production
  (`PackageStrategy`) path, but a `WorkspaceStrategy` override points
  the source strategy at a *UI5 workspace* directory — e.g.
  `ui5-portal/webapp/` — which has nothing to do with the host PHP
  package's `resources/ui5/cards/`. With the override active, the
  resolver looked for the blade in the UI5 workspace and missed it.
  Resolution now bypasses the source strategy entirely: the blade is
  read from `<package-root>/resources/ui5/cards/<slug>.blade.php`,
  where `<package-root>` is derived by reflection on the module class
  file (`dirname($moduleFile, 2)` — assuming the module class lives at
  `<package-root>/src/<X>.php`, the convention the `ui5:*` generators
  emit). Card blades are server-side PHP and now resolve from the
  server-side package layout, regardless of which source strategy the
  module uses for its UI5 client sources. Consumers with a non-standard
  module location can override `getManifest()` on the concrete Card.

### Changed (breaking)

- **`EmitContext` constructor signature.** Now requires a third
  parameter — `Ui5RegistryInterface $registry`. The class's own
  docblock already named the extension pattern ("If a future emitter
  family needs additional cross-cutting state, it's added here once and
  every emitter sees it"), so this is the sanctioned shape. The only
  production caller is `DashboardController`, which is updated; test
  fixtures and any custom emitter chains need to pass a registry too
  (`Mockery::mock(Ui5RegistryInterface::class)->shouldIgnoreMissing()`
  is enough when the chain doesn't include any Cards).
- **Card-endpoint URLs on the dashboard wire.** Now emitted as
  `/ui5/card/<slashed-ns>@<version>/manifest.json` instead of
  `/ui5/card/<dotted-ns>@<version>/manifest.json`. Any client code that
  hard-coded the dotted form breaks — but since the dotted form was
  never actually resolvable by the router, no production consumer was
  relying on it.

## [0.9.8] - 2026-05-23

Dashboard API v0.9 follow-ups. Additive only — no contract breakage,
no SemVer credit spent.

### Added

- **`Card` DTO appearance/sizing seats.** Four consumer-facing
  properties on
  `\LaravelUi5\Core\Ui5\Dashboard\Controls\Sap\Ui\Integration\Widgets\Card`:
  `design` (new `CardDesign` enum — `Solid` / `Transparent`),
  `displayVariant` (new `CardDisplayVariant` enum — `Standard`, `Small`,
  `Large`, `CompactHeader`, `SmallHeader`, `StandardHeader`, `TileFlat`,
  `TileFlatWide`, `TileStandard`, `TileStandardWide`), `width` (default
  `"100%"`), `height` (default `"auto"`). Defaults mirror
  `sap.f.CardBase` / `sap.ui.integration.widgets.Card`. Consumer sets
  them in the artifact's `getCard()` override; `withManifest()`
  preserves them across framework injection.

### Fixed

- **`ui5:tile` now generates a runnable tile.** Since the Dashboard
  API v0.9 pivot (0.9.5), `AbstractUi5Tile::getPayloadProvider()` is
  abstract and `Ui5TileInterface` no longer extends `ResolvableInterface`
  — but the stub still defined the dead `resolve(): string` and omitted
  `getPayloadProvider()`. Any generated tile PHP-fatalled on
  instantiation. `Ui5Tile.stub` now implements `getPayloadProvider()`
  returning `new {Tile}PayloadProvider()`; `TileProvider.stub` now
  implements `PayloadProviderInterface::getPayload()` returning a
  `NumericContent` placeholder; the generator writes the provider file
  as `{Tile}PayloadProvider.php` so the class name matches.

## [0.9.7] - 2026-05-22

Added missing i18n texts for the custom control.


## [0.9.6] - 2026-05-22

Corrected import of sap.f.

## [0.9.5] - 2026-05-22 — SemVer credit spent

First tagged release carrying both the **Parameter API v0.9** and the
**Dashboard API v0.9** as a coherent surface. The two APIs compose: the
Dashboard's leaf emitters resolve their `getRequiredSlots()` through the
Parameter pipeline before producing wire DTOs.

Specs at
[`core-parameter-api-v0.9.md`](../docs/meta/specs/core-parameter-api-v0.9.md)
and
[`core-dashboard-api-v0.9.md`](../docs/meta/specs/core-dashboard-api-v0.9.md).
233/233 tests passing.

## [0.9.4] - 2026-05-05

Added `route()` to `Ui5Registry` for easy url resolving.

## [0.9.3] - 2026-04-29

Bootstrap in correlation to WorkspaceStrategy.

## [0.9.2] - 2026-04-29

Fixed action & resource uris.

## [0.9.1] - 2026-04-22

Added support for Laravel 13.

## [0.9.0] - 2026-04-20

Initial release under BSL 1.1 with `laravelui5/odata` as the OData engine.

### Foundation

- 12-case `ArtifactType` enum (`Module`, `Application`, `Library`, `Card`,
  `Report`, `Tile`, `Kpi`, `Dashboard`, `Action`, `Resource`, `Dialog`,
  `ValueHelp`, `AnalyticsSet`). Enum values are stable and persisted —
  never reassigned.
- Contract-first artifact system: per-type interfaces in
  `src/Ui5/Contracts/`, abstract bases in `src/Ui5/`, cross-cutting
  capabilities in `src/Ui5/Capabilities/`.
- Two-pass `Ui5Registry` loading (instantiate modules, reflect artifacts).
  Discovers `#[Setting]` and `#[Parameter]` attributes. Provides
  namespace↔path conversion and route URL generation.
- Per-request `Ui5ContextInterface` resolved by `ResolveUi5Context`
  middleware.

### OData integration

- `AbstractUi5App` extends `LaravelUi5\OData\ODataService` — every UI5 app
  is an OData service.
- `Ui5ODataServiceRegistry` bridges the artifact registry to odata's
  multi-endpoint routing, parsing `{namespace}@{version}` from request
  paths.
- `ResolveODataEndpoint` middleware binds `ODataServiceInterface` for
  downstream auth.
- `ManifestController` injects OData dataSources into `manifest.json` for
  artifacts implementing `ODataServiceInterface`.
- Core disables odata's auto-route registration and registers odata's
  routes through Core's middleware stack (CSRF, auth).

### Routing

- UI5 route group (`ui5/` prefix) with `web` → `ResolveUi5Context` →
  `EnsureUi5Authenticated`.
- OData route group (`odata/` prefix) with `web` → `FetchCsrfToken` →
  `ResolveODataEndpoint` → `EnsureODataAuthenticated`.

### Source strategies

- Pluggable `Ui5SourceStrategyInterface` with workspace overrides via
  `Ui5SourceOverrideStoreInterface`.
- Self-contained UI5 apps, slugged sources, and no-source endpoint
  detection.

### Settings

- `#[Setting]` attribute with mandatory defaults, value types, read scope
  (`Scope`), and edit authority (`EditLevel`).
- `SettingResolver` injects declarative settings into handlers.
- `AbstractConfigurable` exposes settings as read-only virtual properties.

### Execution

- `ExecutableInvoker` unifies execution of Actions, Cards, Reports, and
  Resources — one parameter resolution + setting injection + FormRequest
  pipeline.

### Scaffolding

- `php artisan ui5:app|sca|library|card|tile|action|resource|report|dashboard`.

### Infrastructure modules

- `CoreModule` (shared library), `DashboardModule`, `ReportModule`
  auto-registered.

### Tooling

- Pest 4 + Orchestra Testbench 10 with SQLite. 64 tests covering registry
  lookup, runtime layer, settings discovery, parameter/setting resolution,
  controllers, and middleware.

### Known caveats

- `ArtifactType::ValueHelp` and `ArtifactType::AnalyticsSet` exist as enum
  cases without interfaces or base classes — Phase 1 and Phase 3 in
  `PLAN.md`.
- Existing `Ui5ReportInterface` works but will be deprecated in favor of
  `Ui5ODataReportInterface` once the OData-backed report path stabilizes
  (Phase 2 / Phase 4 in `PLAN.md`).
