# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [1.0.0-RC5] - 2025-09-
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
