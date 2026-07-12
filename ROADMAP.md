# Roadmap

Core reached **1.0.0 — the freeze — on 2026-06-03**, and **2.0.0 on 2026-07-12**. The artifact contract surface that the entire `0.9.x` line existed to stabilise is signed and under full semantic versioning; 2.0.0 spent a single, deliberate major to mature *how an artifact names its code* (see the [changelog](./CHANGELOG.md)) — the kind of break the bar is now high for, and one we don't expect to repeat. So this roadmap is not a march toward a release; it's a map of *what is frozen*, *what is still provisional and how it graduates*, and *the license horizon*. Order is firm, timing follows the work.

## How Core stabilizes

Every public surface carries an honest stability label (from the 1.0 acceptance program, `docs/meta/specs/core-1.0-acceptance.md`):

- **Frozen** — a public contract under full SemVer. Breaking it requires a **major** release. The `0.9.x` "SemVer credit spent" convention — where breaking changes shipped as patches — **ended at 1.0.0**.
- **Provisional** — shipped and usable, but may still change in a minor, with notice. Labelled, never hidden. A Provisional surface freezes once it has a production authoring consumer that proves the contract.

## Stability today

| Surface | Tier | Status |
|:---|:---|:---|
| Application, Library, Module | Frozen | ✅ Signed |
| Card, Tile, Chart, Dashboard, Report, Action | Frozen | ✅ Signed |
| Parameter API | Frozen | ✅ Signed |
| OData Service Integration | Frozen | ✅ Signed |
| Infrastructure Contributions | Frozen | ✅ Signed |
| Command API / scaffolding (SCA) | Frozen | ✅ Signed |
| Resource API | Provisional | 🚧 Contract pinned; awaiting a production authoring consumer to freeze |
| `AnalyticTile`, `Dialog`, `ValueHelp`, `AnalyticsSet`, `AnalyticCard` | Provisional | ⏳ Enum vocabulary only in Core; the data-binding implementations live in the SDK |

The authoring keystone is frozen with the surface: **"attributes declare, classes do."** An interface *method* carries an artifact's intrinsic contract; a PHP *attribute* (`#[Slot]` / `#[Setting]` / `#[Parameter]`) carries an extrinsic declaration into another subsystem's catalog. **2.0.0 sharpened this:** an artifact's declaration method now *names* its class and the platform builds it, so a declaration only declares.

## What's next

**2.x is the stable home.** 2.0.0 matured the artifact declaration contract — methods name their class, the platform resolves it — the last planned break to that surface, made to lay the groundwork for richer action feedback. Forward work from here is graduation and consolidation, not contract churn:

- **Resource API → Frozen.** Freezes when a production authoring consumer exercises it end-to-end. Until then it stays Provisional with the contract pinned.
- **SDK-bound artifact types.** `AnalyticTile`, `Dialog`, `ValueHelp`, `AnalyticsSet`, and `AnalyticCard` remain enum cases in Core; their real implementations ship and stabilise in the SDK. Core holds the vocabulary so hosts can name them; it does not implement them.
- **Patch / minor cadence.** Bug fixes and additive, backward-compatible surface land as patches and minors under SemVer. Anything that would break a Frozen contract waits for a major and a deprecation window — by design, that bar is now high.

## License horizon

Core is **BSL 1.1**: production use is permitted; repackaging as a competing UI5 toolkit/SDK is prohibited. Each release **converts to Apache 2.0 four years after it ships** — so the 1.0.0 surface (2026-06-03) opens fully on **2030-06-03**, and every later release on its own four-year clock. The fence is what makes giving Core away safe; the horizon is what makes it a fair trade.

## Status legend

- ✅ **Done** — signed / shipped
- 🚧 **In progress** — actively moving toward Frozen
- ⏳ **Queued** — planned, not started
- ⛔ **Blocked** — waiting on an external dependency

## Following along

- **Release notes** — [`CHANGELOG.md`](./CHANGELOG.md), and the GitHub Releases on [`laravelui5/core`](https://github.com/laravelui5/core/releases) (Watch → Custom → Releases to subscribe)
- **Issues** — [github.com/laravelui5/core/issues](https://github.com/laravelui5/core/issues)
- **Documentation** — [laravelui5.com](https://laravelui5.com)
