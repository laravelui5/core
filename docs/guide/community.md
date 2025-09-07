---
outline: deep
---

# Community

LaravelUi5 is actively developed with real-world use cases in mind. While there is no public forum yet, you’re welcome to [open an issue](https://github.com/pragmatiqu/laravel-ui5-core/issues) for bugs, feature requests, or questions.

We’re currently evaluating the best place for community discussions. Stay tuned!

**Want to stay in the loop?**

Subscribe to the [LaravelUi5 newsletter](https://pragmatiqu.io/openui5-into-the-wild/) to receive updates, early previews, tips, and learning resources directly in your inbox.

## License

This package is open-sourced software licensed under the [ASF 2.0](http://www.apache.org/licenses/LICENSE-2.0).

## Contributing

Contributions are welcome and appreciated!

If you’d like to submit a bugfix, improve the documentation, or suggest an enhancement:

1. Fork the repo
2. Create your feature branch (`git checkout -b feature/your-feature`)
3. Commit your changes (`git commit -am 'Add your feature'`)
4. Push to the branch (`git push origin feature/your-feature`)
5. Open a Pull Request

Please make sure to follow PSR-12 and existing architectural conventions. We also recommend discussing larger ideas via an issue before submitting a PR.

## Roadmap

The LaravelUi5 project follows a staged release plan:

**Done**

* [x] Modular UI5 Artifact System
  * [x] `Ui5App` (standalone apps)
  * [x] `Ui5Library` (component libraries)
  * [x] `Ui5Card`, `Ui5Tile`, `Ui5Kpi` (reusable UI blocks)
  * [x] `Ui5Report` (data-driven outputs with export)
  * [x] `Ui5Action` (API-triggered operations)
  * [x] `Ui5Dashboard` (composable UI containers)
* [x] Unified Registry System (`Ui5Registry`)
  * [x] Runtime resolution of artifacts and modules by slug or namespace
  * [x] Manifest-aware integration of all registered components
  * [x] Cached mode for high-performance production use
* [x] Metadata & Introspection
  * [x] `php artisan ui5:sync` to store artifact metadata in DB
  * [x] Consistent handling of `title`, `description`, `namespace`, `url_key`, and `version`
  * [x] URL-safe slugs and unique namespaces enforced
* [x] CLI Scaffolding Suite
  * [x] `ui5:app`
  * [x] `ui5:library`
  * [x] `ui5:card`
  * [x] `ui5:tile`
  * [ ] `ui5:kpi`
  * [x] `ui5:report`
  * [x] `ui5:dashboard`
  * [x] `ui5:action`
  * [x] All commands support namespaced generation, JS/PHP prefixing, and prefilled templates
* [x] Generic Laravel Routing Layer
  * [x] Routes auto-resolve to artifacts via namespace/slug
  * [x] No manual controller or route declaration needed
  * [x] CSRF-safe API calls via `LaravelUi5.call(...)`
* [x] Decide the Manifest property name used for Laravel backend properties, e.g. `laravel.ui5`. 

**Near-Term**

* [ ] Extensive tests.
  * [ ] Unit tests for artifact classes.
  * [ ] Integration tests for registry, sync, and cache.
  * [ ] Snapshot tests for CLI scaffolding outputs.
* [ ] Video course covering all artifact types and wiring patterns.
* [ ] Better error handling in registry access.
  * [ ] Fail-fast helpers (`getOrFail()`, `resolveOrFail()`).
  * [ ] Typed exceptions for missing artifacts or misconfigured modules.
* [ ] Diagnostic commands.
  * [ ] `ui5:validate`, validates all referenced artifacts are in the current deployment.
  * [ ] `ui5:list`, lists all configured artifacts.
  * [ ] `ui5:missing`, same as validate but only reports missing artifacts.
  * [ ] `ui5:help`,	shows all available commands and some getting started
* [ ] Integration docs. Best practices for deployment, caching, and live updates.
* [ ] Scaffold `README.md` for Apps and Libraries

**Mid-Termn**

* [ ] Evaluate formalized artifact lifecycle hooks (`boot()`, `authorize()`, `configure()`, `hydrate()` – optional hooks per artifact type).
* [ ] Review Artifact attributes & reflection metadata.
  * [ ] PHP attributes for type, visibility, permissions, etc.
  * [ ] Used for documentation, introspection, and admin rendering.
* [ ] Registry API endpoints. JSON introspection of artifacts for frontend UIs or dashboards.
* [ ] Audit & telemetry hooks. Hooks for usage tracking, runtime diagnostics, lifecycle tracing.
* [ ] Compatibility matrix with SAP BTP and CAP. Clear documentation where integration is feasible or intentionally decoupled.

For feature requests or roadmap feedback, feel free to [open an issue](https://github.com/pragmatiqu/laravel-ui5-core/issues).

## Support

**Community Support**

For questions or troubleshooting, please check existing issues or open a new one in the [GitHub issue tracker](https://github.com/pragmatiqu/laravel-ui5-core/issues).

**Commercial Support**

Commercial support is available for teams building with LaravelUi5 — including architecture reviews, onboarding, and feature development.

Contact us at [pragmatiqu.io](https://pragmatiqu.io).

