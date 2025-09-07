# LaravelUi5

**Seamless SAP OpenUI5 integration for Laravel backends.**

A production-ready integration layer to run SAP OpenUI5 frontends seamlessly on Laravel backends. Full support for SAP-compliant Security Token Handling (X-CSRF-Token), OData v4 services, and hybrid UI5 + Laravel APIs.

Built to bridge the worlds of enterprise-grade UI5 frontends and modern Laravel application backends. With simplicity, security, and full SAP compatibility in mind.

For comprehensive documentation and usage examples, please visit [https://laravelui5.com](https://laravelui5.com).

## Features

- Native integration of OpenUI5 micro apps into Laravel projects
- Comprehensive UI5 registry for apps, libraries, cards, resources, reports and tiles
- Systematic deployment and versioning model, enterprise-ready
- Centralized proxy, security, and CSRF handling infrastructure
- Action Controller and Settings API enabling process-oriented logic
- Automatic OData $metadata generation from Laravel models
- Supports annotations and typed schemas, enabling real business app scenarios
- Focus on modular, context-driven frontends

## Core vs. SDK

This repository (`laravelui5/core`) provides the **foundation layer**.

- UI5 registry for apps, libraries, actions, resources, tiles, KPIs, and reports
- Multi-endpoint OData integration
- Artifact system and service provider model
- Security infrastructure (proxy, CSRF handling)
- Deployment and versioning model
- Developer tooling through `ui5:*` Artisan commands

The upcoming [`laravelui5/sdk`](https://pragmatiqu.io/laravelui5/sdk) will provide the **developer experience layer**, including

- Business Partner–centric architecture (no direct rights on users, but on strong semantic objects)
- Clear separation of roles (fixed, declarative) and groups (maintainable, context-aware)
- Ui5App awareness with manifest-driven abilities integrated into the Laravel ecosystem
- Flexible context model supporting both global and context-specific assignments
- Consistent interfaces and registries, open for package extensions
- Database layer for the complete artifact system
- Pre-built mini-apps (e.g. Business Partner management with employees, freelancers, clients, etc.)
- Global navigation bar with user avatar, notifications, and a CmdK-like global search
- Pluggable search interface
- Best practices and prebuilt patterns (master/detail floorplan, filter bars, global dialogs, search helpers)
- Clear documentation and testable, maintainable building blocks

Think of **Core** as the runtime engine, and the **SDK** as the developer cockpit.

## Roadmap

See the [Roadmap](./ROADMAP.md) for details on Core status, maintenance, and upcoming SDK features.

## Security

If you discover a security vulnerability, please send an encrypted mail to [security@pragmatiqu.io](mailto:security@pragmatiqu.io).  
A public key will be provided on request until we publish it at laravelui5.com/security.

## Contributing

Contributions are welcome and appreciated!

If you’d like to submit a bugfix, improve the documentation, or suggest an enhancement:

1. Fork the repo
2. Create your feature branch (`git checkout -b feature/your-feature`)
3. Commit your changes (`git commit -am 'Add your feature'`)
4. Push to the branch (`git push origin feature/your-feature`)
5. Open a Pull Request

Please make sure to follow PSR-12 and existing architectural conventions. We also recommend discussing larger ideas via an issue before submitting a PR.

## License

This package is open-sourced software licensed under the [ASF 2.0](http://www.apache.org/licenses/LICENSE-2.0).

## Links

To help you go further with LaravelUi5 and its surrounding ecosystem, this section provides curated links to key resources across UI5, Laravel, and OData. Whether you're exploring SAP Fiori UX, building Laravel-integrated UI5 modules, or deepening your understanding of OData v4, these references offer a solid foundation for development, troubleshooting, and architectural decisions.

**UI5**

* Official OpenUI5 Website
  [https://openui5.org/](https://openui5.org/)  
  → Intro, tutorials, and SDK download.

* OpenUI5 SDK
  [https://sdk.openui5.org/](https://sdk.openui5.org/)  
  → Complete API reference for all `sap.*` libraries.

* UI5 Web Components
  [https://sap.github.io/ui5-webcomponents/](https://sap.github.io/ui5-webcomponents/)  
  → Framework-agnostic UI5-style components for e.g. React, Vue, etc.

* UI5 Tooling
  [https://sap.github.io/ui5-tooling/v3/](https://sap.github.io/ui5-tooling/v3/)  
  → CLI & build system to manage UI5 apps in modern workflows.

* SAP Fiori Design Guidelines
  [https://experience.sap.com/fiori-design-web/](https://www.sap.com/design-system/fiori-design-web/?external)  
  → Best Practices for UI5 Development (Layout, UX, Typography etc.)

**Laravel**

* Laravel Official Docs
  [https://laravel.com/docs](https://laravel.com/docs)  
  → Always the best place to start and return to.

* Laravel News
  [https://laravel-news.com/](https://laravel-news.com/)  
  → Articles, releases, packages, tutorials.

* Laracasts
  [https://laracasts.com/](https://laracasts.com/)  
  → High-quality screencasts for Laravel and the PHP ecosystem.

* Laravel Package Development
  [https://laravel.com/docs/packages](https://laravel.com/docs/packages)  
  → Relevant if you build modular UI5 apps as packages (e.g. in `ui5/`).

* Laravel Artisan Console
  [https://laravel.com/docs/artisan](https://laravel.com/docs/artisan)  
  → Useful when working with your custom `ui5:*` commands.

**Open Data Protocol (OData)**

* Official OData Site (OASIS)
  [https://www.odata.org/](https://www.odata.org/)  
  → Intro, protocol specs, general understanding.

* OData v4 Specification
  [https://docs.oasis-open.org/odata/odata/v4.01/odata-v4.01-part1-protocol.html](https://docs.oasis-open.org/odata/odata/v4.01/odata-v4.01-part1-protocol.html)  
  → Canonical source for query options, function imports, metadata formats.

* Microsoft OData Docs
  [https://learn.microsoft.com/en-us/odata/](https://learn.microsoft.com/en-us/odata/)  
  → Tutorials, tooling, examples (often more accessible than the spec).

* Lodata
  [https://lodata.io](https://lodata.io)  
  → Laravel OData v4 Package that underpins LaravelUi5's OData backend integration.
