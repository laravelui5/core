# Roadmap

This roadmap outlines the current state of **LaravelUi5 Core**, ongoing improvements, and the planned direction for the broader LaravelUi5 ecosystem.

## ✅ Core 1.0 (September 2025)

The foundation layer is complete and ready for production use:

- UI5 Registry and Service Provider integration
- Multi-endpoint OData integration (via lodata fork)
- Self-contained UI5 app scaffolding (`ui5:sca`)
- Artifact model for Apps, Libraries, Resources, Tiles, KPIs, Reports, Dashboards, and Actions
- Security, proxy, and CSRF handling infrastructure
- Deployment and versioning model (enterprise-ready)
- Developer tooling through `ui5:*` Artisan commands

## Maintenance & Improvements

Ongoing work will focus on stability and compatibility:

- Introduce static analysis with PHPStan or Psalm for stronger type safety
- Performance optimizations
- Alignment with upcoming Laravel releases
- Keeping pace with upstream Lodata improvements
- Continuous documentation refinements

## Beyond Core (part of SDK / Ecosystem)

The following features are **not part of Core**, but will be provided through the commercial LaravelUi5 SDK or the surrounding ecosystem:

- Business Partner–centric architecture
- Strict separation of roles (declarative) and groups (maintainable, context-aware)
- Ui5App abilities and manifest-driven extensions
- Flexible context model for global and context-specific assignments
- Global navigation bar with avatar, notifications, and CmdK-like search
- Pluggable search interface
- Best practice UI patterns (master/detail floorplans, filter bars, global dialogs, search helpers)
- Pre-built mini-apps (e.g. Business Partner management)
- Extended training, book, and video courses

👉 See [LaravelUi5 SDK](https://pragmatiqu.io/laravelui5/sdk) for details.

## Feedback & Community

We welcome feedback and contributions via [GitHub Issues](https://github.com/laravelui5/core/issues).

Feature requests outside the Core scope may be considered for the SDK or developed as community packages.

## TODOs

**Write Tests for negative & edge cases**

- Registry loads gracefully with empty config array
- Registry can handle module without artifacts
- Reflection doesn’t crash on attribute-less classes
