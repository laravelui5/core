# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [1.1.3] - 2025-11-
- Save integers for settings enums

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
