# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [4.1.2] - 2026-02-20
- Also allow `Model[]` for setting values

## [4.1.2] - 2026-02-20
- Added modelClass to `Setting` attribute

## [4.1.1] - 2026-02-20
- Renamed cases for enum `EditLevel`

## [4.1.0] - 2026-02-20
- Reworked Setting infrastructure (clear separation of definition, persistence, and resolution)
- Introduced `ReadScope` (replacing `SettingScope`) to explicitly model resolution hierarchy
- Introduced `EditLevel` (replacing `SettingVisibilityRole`) to clearly separate edit authority from read scope
- Aligned `EditLevel` hierarchy to ascending authority (`>=` comparison, higher value = higher privilege)
- Made default values mandatory in `#[Setting]` attribute
- Clarified separation between read resolution (`ReadScope`) and write authorization (`EditLevel`)
- Simplified setting scope model (artifact-centric resolution; removed team scope)
- Normalized terminology across settings (ValueType, ReadScope, EditLevel)

## [4.0.1] - 2026-02-19
- infrastructure before contributed modules

## [4.0.0] - 2026-02-19
- Changed `get` to `getArtifact` in `Ui5RegistryInterface`
- Added `getModuleByClass` and `getArtifactByClass` in `Ui5RegistryInterface`

## [3.5.1] - 2026-02-19
- Enable late static binding in fromArray() factory

## [3.5.0] - 2026-02-08
- Adopted tests
- Auth redirect only if login route exists
- Refactored directory layout for `Ui5ActionInterface` instances
- Feature artifacts from now on get their namespaces from their hosts 
- Refactored directory layout for `Ui5CardInterface` instances
- Moved `getType` into abstract classes
- Refactored directory layout for `Ui5DashboardInterface` instances
- Refactored directory layout for `Ui5ReportInterface` instances
- Refactored directory layout for `Ui5ResourceInterface` instances
- Refactored directory layout for `Ui5TileInterface` instances (no final implementation yet!)
- Added SP to composer extra section

## [3.4.2] - 2026-02-07
- Allow public access to core lib

## [3.4.1] - 2026-02-07
- Correct call hierarchy

## [3.4.0] - 2026-02-07
- Stabilize UI5 routes and move auth handling to route-level middleware
- Added `ODataAuthGate` and `Ui5AuthGate`
- Removed `systems` config switches together with `active`

## [3.3.3] - 2026-02-07
- allow container service injection in executable handlers

## [3.3.2] - 2026-02-07
- Corrected caller context for initModels

## [3.3.1] - 2026-02-07
- Updated JS lib
- Instantiate convenience models resources & meta

## [3.3.0] - 2026-02-07
- Refine manifest contribution contract

## [3.2.0] - 2026-02-06
- Removed enum `AbilityType`
- Removed attribute `HideIntent`
- Added interface `Ui5Infrastructure` to mark modules that are inherent to the LaravelUi5 universe

## [3.1.0] - 2026-02-03
- Introduced a centralized `ExecutableInvoker` to handle execution of Actions, Cards, Reports, and Resources in a uniform way.
- Added a first-class `SettingResolver` to inject declarative settings (`#[Setting]`) into handlers and providers.
- Added `AbstractConfigurable` base class to provide read-only, virtual access to injected settings.
- Refactored `ActionDispatchController`, `CardController` (and upcoming Report/Resource controllers) to delegate execution logic to `ExecutableInvoker`.
- Unified parameter resolution, setting injection, FormRequest handling, and method invocation across all executable artifacts.
- Simplified controller implementations by removing duplicated reflection and injection logic.
- Removed implicit execution logic duplication across individual controllers.
- Removed the need for per-controller parameter and setting resolution boilerplate.
- Added production-ready frontend bundle

## [3.0.4] - 2026-01-19
- Added ODataController Test

## [3.0.3] - 2026-01-19
- Removed version from OData route

## [3.0.2] - 2026-01-19
- Keep string to Ui5ModuleInterface map in Ui5Registry

## [3.0.1] - 2026-01-19
- Changed visibility of `$systemMiddleware`

## [3.0.0] - 2026-01-19
- Remove Sluggable/SlugSettable interfaces
- Make namespace@version the sole artifact identity
- Simplify Ui5Registry to build-time, namespace-based lookup
- Separate module identity from artifact identity
- Reduce ArtifactType to semantic role and route prefix
- Move resolution logic fully into middleware
- Renamed Integration Tests
- Added Action Test
- Register OData routes with const prefix
- Added `resolvePath` to SourceStrategyInterface
- Added `CardControllerTest`
- Added `DashboardControllerTest`
- Added `ResourceControllerTest`
- Adopted configuration

## [2.4.9] - 2026-01-17
- Fetch XSRF-Token early

## [2.4.8] - 2026-01-16
- NoSource SourceStrategy for pragmatic Endpoint detection

## [2.4.7] - 2026-01-16
- Corrected Namespace

## [2.4.6] - 2026-01-16
- added PHP tags

## [2.4.5] - 2026-01-16
- add @IncludeIfSdk directive with Ui5ShellFragment capability check

## [2.4.4] - 2026-01-16
- added namespace to shell manifest enhancement

## [2.4.3] - 2026-01-15
- Consolidated AbstractUi5Manifest class

## [2.4.2] - 2026-01-14
- Every artifact belongs to a module
- tightened return type of `getArtifactRoot`
- Upgraded default OpenUI5 library load to 1.136.11
- Tightened loading of JS artifacts for PRO mode
- added all dbg sources for core JS lib
- added extension for body scripts
- tweaked Composer build resources

## [2.4.1] - 2026-01-11
- Hand the registry over to shell fragment builder

## [2.4.0] - 2026-01-11
- JS: Introduced com.laravelui5.core.BaseComponent as the shared base UI5 component
- Simplified dialog artifact registration in Ui5Registry
- Removed dialog-specific configuration switches and slug handling

## [2.3.4] - 2025-12-29
- Added getter for introspection objects
- Removed `getDescriptor` from `Ui5Source`

## [2.3.3] - 2025-12-29
- Ordering detection of source strategy

## [2.3.2] - 2025-12-29
- Added source strategy for self-contained UI5 apps

## [2.3.1] - 2025-12-29
- Changed visibility of `sourceOverrides` in `Ui5Registry`
- Added `Ui5SourceOverrideStoreInterface` and implementation
- Added `Ui5SourceStrategyResolverInterface` and implementation

## [2.3.0] - 2025-12-29
- Namespace refactoring

## [2.2.1] - 2025-12-29
- Added `Ui5SourceStrategyInterface` to for resolving packaged artifact information
- Consolidated test setup for common patterns

## [2.2.0] - 2025-12-28
- Move source path resolution back to Ui5Registry
- Remove Ui5SourceMap and implicit source attachment
- Resolve UI5 sources lazily via App and Library artifacts
- Introduce class-based workspace source overrides
- Align tests and fixtures with real package layout 

## [2.1.3] - 2025-12-27
- Fixed name for Ui5ModuleLib scaffolding

## [2.1.2] - 2025-12-27
- Fixed single quotes for `Ui5Module->getName()` scaffolding
- Fixed quotes in `--vendor` default for app scaffolder

## [2.1.1] - 2025-12-27
- Fixed export .ui5-sources.php

## [2.1.0] - 2025-12-27
- Made Ui5CoreContext final readonly
- Moved `ConfigurableInterface` and `ParameterizableInterface` to Core namespace
- Removed `SluggableInterface` from `Ui5AppInterface` and `Ui5LibraryInterface`
- Added `getSource` to `Ui5ModuleInterface`
- Added introspection capabilities for Ui5Apps via `Ui5SourceMap` and corresponding classes
- Added introspection capabilities for Ui5Libraries via `Ui5SourceMap` and corresponding classes
- Complete refactoring of `GenerateUi5LibraryCommand`
- Complete refactoring of `GenerateUi5AppCommand`
- Enhanced `Ui5AppInterface` for introspection
- Enhanced `Ui5LibraryInterface` for introspection
- Created abstract classes for Ui5Apps and Ui5Libraries
- Created tests for all value object factories

## [2.0.1] - 2025-12-24
- Made `Ui5ArtifactInterface->getModule` mandatory
- Added `#[HideIntent]`

## [2.0.1] - 2025-12-22
- Introduced Ui5ContextInterface

## [2.0.0] - 2025-12-22
- slim Core to pure UI5 runtime and URI resolution

## [1.2.6] - 2025-12-14
- Fix module ownership and binding of dashboards, reports, and dialogs

## [1.2.5] - 2025-12-14
- registry/runtime binding to avoid aliasing authoritative state

## [1.2.4] - 2025-12-14
- Introduced a pluggable UI5 artifact identification pipeline

## [1.2.3] - 2025-12-14
- enhance route resolution to explicitly support infrastructure-level routes

## [1.2.2] - 2025-12-12
- Added docs to config
- Externalize slugs for globally exposed UI5 artifacts

## [1.2.1] - 2025-12-03
- Changed visibility of `systemMiddleware` in `Ui5CoreServiceProvider`

## [1.2.0] - 2025-12-02
- Renamed `sdk` namespace in manifest to `vendor`
- `enhanceFragment` now directly plugs into `vendor` namespace
- corrected PHP doc in `Ui5RegistryInterface` for `modules()` and `artifacts()`
- removed method target
- Introduced `Ui5ModuleInterface::getName()` to provide a canonical, stable module identifier.
- Added `Ui5ModuleInterface::getAllArtifacts()` to return a complete, flat list of all artifacts belonging to a module.
- Added first-class support for module-level artifacts: `getReports()`, `getDashboards()`, `getDialogs()`

## [1.1.15] - 2025-11-25
- Added settings with default values to manifest
- Added shell interface and namespace to manifest

## [1.1.14] - 2025-11-23
- Renamed Ui5Module to AbstractUi5Module
- Removed unnecessary tests
- Renamed GenerateUi5Resource to GenerateUi5ResourceCommand
- Removed AppContextInterface and implementation
- Renamed ReportActionDispatcher to ReportActionDispatchController

## [1.1.13] - 2025-11-23
- Undo changes from 1.1.12
- removed abilities, roles, and settings from manifest

## [1.1.12] - 2025-11-22
- Reworked Ui5RuntimeInterface.php

## [1.1.11] - 2025-11-22
- Added blog to documentation
- Added API docs to documentation
- Focused README and ROADMAP on Core
- Added article *LaravelUi5 Explained*
- Added docs on configuration
- Added canonical URL to head
- Fixed Ui5Manifest.stub

## [1.1.10] - 2025-11-15
- Save role scope to registry
- Removed ContextServiceInterface and related interfaces and services

## [1.1.9] - 2025-11-15
- Use the runtime instead of the registry
- Renamed AbstractLaravelUi5Manifest to AbstractManifest
- Added InvalidModuleException and throw when module unknown in AbstractManifest
- Implemented missing methods in AbstractManifest
- Added route name for app route
- Renamed getLaravelUi5Fragment to getFragment
- Renamed TestCase to FeatureTestCase for more clarity
- Added optional property `scope` to Role attribute

## [1.1.8] - 2025-11-13
- Split Introspection and Runtime nature of Ui5RegistryInterface
- Added new interface Ui5RuntimeInterface
- Renamed method `all` in `Ui5Registry` to `artifacts`

## [1.1.7] - 2025-11-13
- Simplified slugFor in registry
- Removed namespaceToModuleSlug and artifactToModuleSlug from registry interface

## [1.1.6] - 2025-11-13
- Unified Core role declarations with `SettingVisibilityRole` enum for consistent hierarchy and synchronization.
- Added artifact to module slug mapping
- renamed introspect function on registry to exportToCache

## [1.1.5] - 2025-11-12
- Include namespace when referencing abilities for roles

## [1.1.4] - 2025-11-12
- Resolve referenced abilities for roles

## [1.1.3] - 2025-11-11
- Save enums as objects in registry
- Renamed visibilityRole to role in Setting
- Enhanced Role attribute
- Added note property to Setting attribute
- normalized technical identifiers in attributes
- added distinct base class for unit tests

## [1.1.2] - 2025-11-09
- Added extension hook to Ui5Registry
- Refined PHPDoc type annotations
- Renamed property description to note on attribute Role
- remove laravel/framework dependency

## [1.1.1] - 2025-11-08
- Added new `AbilityType::Access`. Introduces backend-only ability type for controlling access to entry-level artifacts (Apps, Dashboards, Reports, Tiles, KPIs, Resources, Dialogs).
- Enhanced `Ui5Registry` accessors for abilities and settings

## [1.1.0] - 2025-11-07
- Added tests for Ui5Registry
- Switched to PHP 8.3 language level
- Moved CachedUi5Registry to the Sdk
- Enhanced Ui5Registry interface
- Included Settings reflection in Ui5Registry
- Enhanced metadata reflection for manifest.json

## [1.0.2] - 2025-10-16
- Always initialize baseUrl in Connection.js
- Added Dialog to ArtifactType and a new interface for global dialogs, Ui5DialogInterface
- Added Installation to SettingScope default for setting values provided by the dev

### Added
- SemanticObject attribute to identify main business object of a module 
- SemanticLink attribute to annotate Eloquent relationships for links on semantic model objects
- Ability attribute to tag classes with semantic authorization keys 
- Role attribute to tag modules with semantic authorization bags

## [1.0.1] - 2025-10-13
- Make mainService URI optional in ui5-core-lib

## [1.0.0] - 2025-09-25
- First public release
- Set the correct videos

## [1.0.0-RC6] - 2025-09-23
- Move namespace of core lib from io.pragmatiqu to com.laravelui5
- added the video course

## [1.0.0-RC5] - 2025-09-20
- Corrected ParameterResolver
- Parameters have to be declared on the ActionHandler (not the Ui5Action)

## [1.0.0-RC4] - 2025-09-19
- Documentation tweaks
- AbstractActionHandler implements ParameterizableInterface

## [1.0.0-RC3] - 2025-09-17
- Removed optional dependencies
- Refactored Ui5ReportInterface. Exports should be implemented as dedicated report actions

## [1.0.0-RC2] - 2025-09-09
- Added missing function to the module templates

## [1.0.0-RC1] - 2025-09-08

### Added
- Initial public release of LaravelUi5 Core
- Seamless integration of SAP OpenUI5 with Laravel backends
- Support for OData v4 (via fork of flat3/lodata)
- Developer tooling: `php artisan ui5:*` commands
