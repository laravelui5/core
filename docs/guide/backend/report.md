---
outline: deep
---

# UI5Report

## Introduction

A `Ui5Report` is a specialized type of UI5 artifact within your Laravel UI5 app. It presents structured business data, typically for display in the browser, for download as a PDF, or for export as an Excel file.

Reports can fulfill a variety of purposes, including:

* Business analysis (e.g. "Unbilled Hours")
* Period-end closing (monthly or annual)
* Controlling & project settlement
* Operational listings or evaluations
* Tax documentation or accruals

## Conceptual Overview

Reports in LaravelUi5 are first-class artifacts that combine backend data logic with frontend rendering.

They are always part of an app module and follow a well-defined lifecycle.

### Report Lifecycle

A UI5 report has *three distinct phases*.

#### 1. Selection Phase

The selection screen is built using:

* `Report.controller.js` (UI5 logic)
* `Report.view.xml` (UI layout)

The controller collects user input and submits it to the backend. Inputs are mapped to `#[ReportParam(...)]` attributes on the report’s `ReportDataProvider`.

```php
#[ReportParam(name: 'month', type: 'string')]
#[ReportParam(name: 'employee_id', type: 'int', required: false, modelClass: Employee::class)]
```

#### 2. Rendering Phase

The actual report is rendered via:

* `report.blade.php` (Laravel view)
* `getViewData(array $context)` from `ReportDataProviderInterface`

This view is loaded inside an `iframe` in the UI5 app. It can contain tables, charts, conditional logic, etc.

#### 3. Action Phase (Optional)

Report actions implement `ReportActionInterface` and can:

* update data
* trigger background jobs
* perform bulk modifications

They are declared in the report metadata and rendered as UI buttons. The report context (user inputs) is passed automatically to the action.

### Core Interfaces

`ReportDataProviderInterface`

* `getEntityType()` returns report metadata (columns, types, labels)
* `getViewData(array $context)` returns data for HTML rendering
* `getExportData(array $context)` (optional) for CSV/XLSX/PDF generation
* `getReportName()` defines export file names

`ReportActionInterface`

* `label()`, `description()`, `execute(array $context)`
* Triggered from the frontend via UI5 interaction

`#[ReportParam(...)]`

* Declarative parameter injection
* Type-safe and model-aware

## How to Generate

The `ui5:report` command scaffolds all required artifacts to implement a UI5-compliant report inside a given application module.

It follows the LaravelUi5 architectural principles:

* Artifacts are generated *explicitly in code* — no magic
* Reports are *registered within a specific app* (e.g. Offers, Timesheet)
* Resources are *cleanly structured* in the file system
* Logic is guided by *interfaces, attributes, and conventions*

Run the following:

```bash
php artisan ui5:report Timesheet/Hours \
  --title="Booked Hours" \
  --description="Hours booked by employees" \
  --formats=html,pdf,xlsx \
  --actions=discardHours,approveHours
```

This will create a report called `Hours` inside the `Timesheet` app module.

## Options

| Option            | Default                           | Description                                       |
|:------------------|:----------------------------------|:--------------------------------------------------|
| `name` (arg)      | *(required)*                      | Format: `AppNameReportName` (e.g. `SalesSummary`) |
| `--php-ns-prefix` | `Pragmatiqu`                      | PHP namespace root                                |
| `--title`         | Report class name                 | UI-facing report title                            |
| `--description`   | `Report generated via ui5:report` | Metadata description                              |
| `--formats`       | `html,pdf`                        | Supported formats (`html`, `pdf`, `xlsx`, etc.)   |
| `--actions`       | *(optional)*                      | Comma-separated list of backend action classes    |

## Output

Given `TimesheetHours`, the following structure is created:

```
ui5/
└── Timesheet/
    ├── src/
    │   └── Reports/
    │       └── Hours/
    │           ├── Report.php
    │           ├── ReportDataProvider.php
    │           ├── DiscardHoursAction.php
    │           └── ApproveHoursAction.php
    └── resources/
        └── ui5/
            └── reports/
                └── hours/
                    ├── Report.controller.js
                    ├── Report.view.xml
                    └── report.blade.php
```

## Artifact Overview

### PHP

**Report.php** (`Ui5ReportInterface`)

Defines the report artifact with metadata and structural references:

> 💡 A report may also expose a full OData structure via `getEntityType()`, which allows UI5 tables to bind automatically and filters to be generated dynamically.

**ReportDataProvider.php** (`ReportDataProviderInterface`)

Implements the core logic of the report:

* Fetching and preparing the dataset
* Generating structured exports (PDF, Excel, CSV)
* Defining visible columns and metadata

**Actions** (Optional)

If `--actions=...` is provided, each will generate a class that implements `ReportActionInterface`.

These actions may trigger backend workflows, mark reports as processed, or send notifications.

### Frontend

* `Report.controller.js`: Contains logic for selection screen
* `Report.view.xml`: Selection UI (inputs, filters)
* `report.blade.php`: Actual report content rendered in iframe

## Module Integration

Reports are *subordinate artifacts* and must be *manually wired* into their module:

```php
public function getReports(): array
{
    return [
        new Reports\Hours\Report(),
    ];
}
```

## Advanced Capabilities (*planned*)

Thanks to the structural nature of reports, additional features are possible:

* *UI5 table personalization*: column selection, filter variants, sorting
* *OData binding*: via auto-generated `$metadata` from `EntityType`
* *Export consistency*: PDF and Excel formats use the same column definitions
* *CI/branding integration*: tenant-specific logos and labels are injectable
* *Component-based rendering*: reports are displayed as isolated UI5 mini-apps

## Sample Use Case: “AnnualCutOff Report”

A classic scenario: Displaying all non-billable hours of the current year, with the option to archive them.

**Flow**

1. User selects a year → clicks "Run"
2. Report displays project totals in a table
3. User clicks dropdown action: “Archive”
4. Backend updates status or creates a booking

## Related Links

* [Modules](./module)
* [Lodata EntityTypes](https://lodata.io/modelling/types/collections.html)
* [OpenUI5 Documentation](https://sdk.openui5.org/)
* [Laravel Blade](https://laravel.com/docs/12.x/blade#main-content)
