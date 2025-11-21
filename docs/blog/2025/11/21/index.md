---
author: mgerzabek
title: LaravelUi5 Explained
teaser: The Metadata Engine That Makes Laravel Self-Describing
---

# LaravelUi5 Explained

> LaravelUi5 is a metadata engine designed for teams and agencies building enterprise UIs for SMEs, where long-term maintainability matters more than framework trends.

Modern Laravel applications have no shortage of frontend options.
[Livewire]{target="_self"} is excellent for local UI state, [Inertia.js]{target="_self"} brings a smooth request–response model to [Vue]{target="_self"} and [React]{target="_self"}, and single-page frameworks offer endless flexibility.  
These tools work remarkably well for product teams building dashboards, CRUD flows, or customer-facing interfaces.

But as soon as projects grow into enterprise UIs, where teams model deep domains, navigate across modules, coordinate long-lived workflows, and maintain shared semantics, the limits of their chosen stack begin to show.

The backend evolves through clear domain language, while the frontend recreates the same structures in a different vocabulary: models mirrored as JSON schemas, capabilities duplicated as permissions, navigation hard-coded per module.  
Everything drifts by a fraction, update by update, until the system no longer feels like one system.

[OpenUI5]{target="_self"} approaches this problem from the opposite direction.
Rather than asking every project to invent its own UI architecture, it provides a disciplined, enterprise-ready [design language]{target="_self"} that scales through patterns, semantics, and declarative structure.
Its learning curve is real, but so is its clarity.

*LaravelUi5* brings that clarity into the [Laravel]{target="_self"} world.

Instead of synchronizing backend and UI through conventions or configuration files, it gives a Laravel application a way to describe itself: its modules, artifacts, semantics, and relationships.

Once the system expresses these structures in code, both backend and UI operate from the same source of truth.

That descriptive foundation is the heart of LaravelUi5.

This article explains how [laravelui5/core]{target="_self"} works internally and how it turns a Laravel project into a self-describing platform that both backend and UI can rely on.

## Code as the Single Source of Truth

LaravelUi5 is not a UI framework.  
And that clarity matters.

Instead of providing components and templates, it introduces a metadata layer that lets your Laravel codebase describe itself.

Its job is simple and powerful.

> Let every artifact in a LaravelUi5 system describe itself in code and make that description discoverable at runtime.

Once the system can express its own structure in code, it establishes a shared vocabulary for the entire UI layer that becomes the single, consistent source of truth every module and app can build on.

Its structure rests on three core concepts that shape everything built on top.

### 1. The Artifact Taxonomy

Is a formal classification of the available UI [artifact types]{target="_self"} so the system can reason about

- [Modules]{target="_self"}
- [Apps]{target="_self"}
- [Libraries]{target="_self"}
- [Cards]{target="_self"}
- [Reports]{target="_self"}
- [Tiles]{target="_self"}
- [KPIs]{target="_self"}
- [Dashboards]{target="_self"}
- [Actions]{target="_self"}
- [Resources]{target="_self"}
- and [Dialogs]{target="_self"}.

Together, these elements form a complete semantic model of your system.
When Core scans your codebase, it turns these declarations into a registry that both backend and UI5 can consume.

### 2. POPOs

In LaravelUi5, everything begins with small, descriptive classes.

If you know Java’s POJOs – *Plain Old Java Objects* – the idea is the same here in PHP.
Plain Old PHP Objects whose only job is to describe what an artifact is.

Every artifact type corresponds to its own POPO class.
And to keep things consistent, LaravelUi5 provides an Artisan command for each one.

To create an app abstraction, simply run `php artisan ui5:app <NAME>` and all relevant code pieces are scaffolded for you.

In addition to the POPO itself, each Artisan command also generates the supporting classes that give the artifact its behavior.

For an Action, for example, the scaffolder creates the *Action* POPO – the metadata declaration – and the [Handler]{target="_self"} class, where you implement the logic.

The idea is simple: POPOs describe the artifact; the generated classes give you the place to implement what it does.

This keeps metadata cleanly separated from logic while giving you a predictable structure for every artifact you add to the system.

### 3. Typed Attributes

If POPOs define the artifacts, typed Attributes define their meaning.

LaravelUi5 uses native PHP attributes to express the *semantics* of each artifact, what it represents, how it relates to others, what it can do, and what it needs to operate.

These attributes naturally fall into three conceptual domains, each shaping a different part of the system’s behavior.

**Domain Semantics**

At the architectural level, every module represents a single domain concept, its [Semantic Object]{target="_self"}.
This keeps modules cohesive and makes the system predictable: one module, one domain.

From there, a [Semantic Link]{target="_self"} expresses how these domain concepts relate across modules.
These links form the semantic graph that the UI and backend can both navigate, enabling cross-module intent resolution without hard coupling.

In practice, this turns your codebase into a set of clearly defined domains connected by explicit relationships. A structure that would be almost impossible to maintain through configuration files.

**Runtime Semantics**

[Parameters]{target="_self"} describe what an action or resource needs in order to execute.
This addresses a familiar tension found in many systems: the frontend trying to infer what the backend needs. By making parameters explicit, LaravelUi5 removes that guesswork and replaces it with a clear, discoverable contract.

[Settings]{target="_self"}, on the other hand, surface configuration values that end users or administrators can influence.
Because they are declared in code, Core can expose them to the UI automatically, keeping configuration transparent and consistent across modules.

Together, parameters and settings give the runtime a clear contract by defining what the system requires and what the user can adjust.

**Capability Semantics**

Finally, [Abilities]{target="_self"} describe what a user can conceptually do within a domain, such as approving invoices, locking users, or exporting data, while [Roles]{target="_self"} group these abilities into meaningful sets.

By keeping this layer purely declarative, abilities and roles remain part of the system’s semantic model rather than its implementation details.

In practice, they form the capability vocabulary of the platform and provide a stable contract that both backend and UI can rely on when reasoning about what the system allows.

**A Unified Semantic Model**

When combined, these three domains form a complete semantic description of your application.
The vocabulary now answers the most crucial questions, each system evokes – 
*What does this module represent?* *What does it need?* *What can it do?*

With these elements aligned, the system has a coherent language it can use to describe itself.

## The Architecture of the Metadata Engine

LaravelUi5/Core is structured as a clean, four-layer architecture.  
Each layer has a single responsibility and fits naturally into the developer’s mental model. It begins with the code you write, where Core derives the metadata that feeds the runtime, which in turn enriches the Manifest consumed by the UI.

```
[Manifest]    ← system compilation (UI5-facing)
[Runtime]     ← operational engine (middleware, controllers)
[Reflection]  ← building the metadata graph
[Abstraction] ← developer vocabulary and module boundary
```

These layers form a vertical path from *Abstraction*, where UI5 modules are discovered and integrated into the Laravel host; 
to *Reflection*, where those definitions are turned into metadata;
to *Runtime*, where that metadata becomes executable behavior;
and finally to *Manifest*, where the system presents itself to the UI5 runtime.

Let’s walk through each layer from bottom to top.

### Abstraction Layer — Where Laravel Meets UI5

The abstraction layer marks the point at which a UI5 application becomes part of the Laravel ecosystem. When a developer uses the relevant Artisan command to create a UI5 app, LaravelUi5 binds that app to a UI5 module – a small domain capsule that anchors the UI5 project within the host application.

Each module contains exactly one root artifact, either an [App]{target="_self"} or a [Library]{target="_self"}, but never both.
That root defines the role the module plays: an App becomes an entry point in the UI, while a Library provides shared resources and capabilities for other modules.
Everything else the module contributes belongs to this capsule and derives its meaning from it.

This is also where the vocabulary from the previous section begins to shape the module.
At this level, the system learns what exists and how it is organized, without yet interpreting or executing any of it.

Conceptually, the abstraction layer gives every UI5 module a home within Laravel, turning a standalone build artifact into a structured part of the application.
It forms the foundation upon which deeper understanding becomes possible and the point from which the system can begin to reason about the modules developers create.

### Reflection Layer — Turning Structure Into Meaning

Once modules are anchored inside the application, the reflection layer turns their declarations into a coherent metadata model.
Core reads the modules registered in the [configuration]{target="_self"} and passes each one to the [Ui5Registry]{target="_self"}, which inspects their POPOs and typed attributes.

Through this process, the system discovers which artifacts a module contributes, which semantic object it represents, how modules relate to one another, and which capabilities they declare.
Reflection simply transforms these code-level descriptions into a structured graph the system can reason about.

The result is a complete, in-memory representation of the application’s semantic model.
This graph is the foundation the upper layers build upon, enabling behavior in runtime and a consumable structure in the manifest.

### Runtime Layer — Making Metadata Executable

The Runtime layer uses the metadata discovered through reflection to serve UI5 applications in a predictable and consistent way.

When a UI5 app is requested, Laravel handles the route, loads the module context through middleware, and returns an enriched index.html that the UI5 runtime can bootstrap.  
From there, the client loads OpenUI5, fetches the app’s manifest.json, and receives an enhanced version of it through the same metadata-aware pipeline.

Actions, resources, and other artifact endpoints follow the same pattern.
The runtime simply applies the metadata graph to resolve modules, locate artifacts, and produce the correct response.

This layer is intentionally thin.
Its job is not to decide who may do something, but to ensure that everything the system describes can be reached, invoked, and served in a consistent way.

A typical request flow looks like this.

```
UI5 frontend
   ↓
Laravel route (/ui5/action/users/toggleLock)
   ↓
ResolveUi5Context → middleware that loads metadata
   ↓
ActionDispatchController → finds the Ui5Action POPO and resolves parameters and settings
   ↓
Action::handle() → executes backend logic
   ↓
Response returned
```

With the runtime layer clarified, we can now look at how the system exposes its structure to the UI5 client.

### Manifest Layer — Presenting the System to UI5

The manifest layer transforms the metadata graph into a structure that the UI5 runtime can consume.
Where reflection builds a complete picture of the system, the manifest extracts the parts the frontend needs: navigation, actions, resources, settings, and the semantic intents that link modules together.

When a UI5 application starts, it loads its `manifest.json`.
LaravelUi5 extends this document with a dedicated section that contains the information derived from the registry, a compact summary of what the module offers and how it fits into the larger system.

This allows the UI to understand where it is, which capabilities it can call, and how to reach related parts of the application.

As a result, UI5 applications become introspectable: they can navigate by semantic intent, discover backend actions, and adapt their behavior based on the roles, abilities, and settings exposed by the backend.

This final layer closes the loop.

What began as simple declarations in code now becomes a fully self-describing interface between Laravel and UI5. This is the moment where the metadata engine reveals itself to the client.

## Putting It All Together

A LaravelUi5 system begins with clear, descriptive code.  
Modules define their domain, artifacts describe their capabilities, and attributes give each element its meaning.
Reflection turns these descriptions into a coherent metadata graph, Runtime makes that graph executable, and the Manifest exposes the resulting structure to UI5.

The outcome is a Laravel application that can describe itself consistently, predictably, and without configuration files.
Every layer supports the next, creating a system in which backend and UI share the same vocabulary and operate from the same source of truth.

LaravelUi5 does not change how Laravel developers work.
It gives structure to what they already do, allowing the system to express its intent in a way both humans and the UI can rely on.

## Benefits for Laravel Teams

LaravelUi5 brings clarity to an area where most systems fragment.  
By letting the application describe itself, it replaces ad-hoc conventions with a consistent structure the entire stack can rely on.

**Single source of truth**  
SSoT means no more duplicated definitions. The backend becomes the single source of truth; the UI reads from the same model instead of maintaining parallel schemas or configuration files.

**Predictable behavior**  
Artifacts, actions, and semantic links are discoverable rather than stitched together.
Every module knows what it represents, which capabilities it exposes, and how it connects to the rest of the system.

**A modular architecture that scales**  
Modules remain self-contained: each one expresses its own domain, vocabulary, and UI assets.
As the system grows, modules can be added or removed without reshaping the whole application.

**A living form of documentation**  
Because metadata is derived from code, the system stays accurate as developers refactor, extend, or reorganize modules.
The registry becomes a continuously updated map of the application’s structure.

Together, these qualities shift the burden away from manual coordination and toward a model the system can maintain on its own, bringing a level of consistency and clarity to Laravel applications that typically requires far more infrastructure.

In a world where frontend architectures constantly change, this stability and clarity is a real advantage.

> `LaravelUi5` is the metadata engine that makes Laravel systems predictable, discoverable, and ready for enterprise scale.

<!-- References -->
[Livewire]: https://livewire.laravel.com/
[Inertia.js]: https://inertiajs.com/
[Vue]: https://vuejs.org/
[React]: https://react.dev/
[OpenUi5]: https://sdk.openui5.org/
[design language]: https://www.sap.com/design-system/fiori-design-web/
[Laravel]: https://laravel.com/
[laravelui5/core]: https://packagist.org/packages/laravelui5/core
[artifact types]: /api/LaravelUi5.Core.Enums.ArtifactType.html
[Modules]: /api/LaravelUi5.Core.Ui5.Contracts.Ui5ModuleInterface.html
[Apps]: /api/LaravelUi5.Core.Ui5.Contracts.Ui5AppInterface.html
[Libraries]: /api/LaravelUi5.Core.Ui5.Contracts.Ui5LibraryInterface.html
[Cards]: /api/LaravelUi5.Core.Ui5.Contracts.Ui5CardInterface.html
[Reports]: /api/LaravelUi5.Core.Ui5.Contracts.Ui5ReportInterface.html
[Tiles]: /api/LaravelUi5.Core.Ui5.Contracts.Ui5TileInterface.html
[KPIs]: /api/LaravelUi5.Core.Ui5.Contracts.Ui5KpiInterface.html
[Dashboards]: /api/LaravelUi5.Core.Ui5.Contracts.Ui5DashboardInterface.html
[Actions]: /api/LaravelUi5.Core.Ui5.Contracts.Ui5ActionInterface.html
[Handler]: /api/LaravelUi5.Core.Ui5.Contracts.ActionHandlerInterface.html
[Resources]: /api/LaravelUi5.Core.Ui5.Contracts.Ui5ResourceInterface.html
[Dialogs]: /api/LaravelUi5.Core.Ui5.Contracts.Ui5DialogInterface.html
[Semantic Object]: /api/LaravelUi5.Core.Attributes.SemanticObject.html
[Semantic Link]: /api/LaravelUi5.Core.Attributes.SemanticLink.html
[Parameters]: /api/LaravelUi5.Core.Attributes.Parameter.html 
[Settings]: /api/LaravelUi5.Core.Attributes.Setting.html
[Abilities]: /api/LaravelUi5.Core.Attributes.Ability.html
[Roles]: /api/LaravelUi5.Core.Attributes.Role.html
[App]: /guide/backend/app.html
[Library]: /guide/backend/library.html
[Ui5Registry]: /api/LaravelUi5.Core.Ui5.Contracts.Ui5RegistryInterface.html 
[configuration]: /guide/backend/configuration.html
