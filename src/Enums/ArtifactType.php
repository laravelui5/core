<?php

namespace LaravelUi5\Core\Enums;

use LaravelUi5\Core\Exceptions\NonRoutableArtifactException;
use LaravelUi5\Core\Ui5\Contracts\Ui5ActionInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5CardInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5DashboardInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5KpiInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5LibraryInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ReportInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5TileInterface;

/**
 * Enum representing the types of UI5 artifacts supported by the system.
 *
 * Each artifact type corresponds to a distinct kind of module or UI component
 * that can be registered, versioned, and deployed within the Laravel-based UI5 integration.
 *
 * @see Ui5ArtifactInterface
 */
enum ArtifactType: int
{
    /**
     * A UI5 module providing a logical container for artifacts such as applications,
     * cards, KPIs, actions, or libraries. Modules define the namespace scope and slug
     * used for routing and access control, and typically correspond to a PHP package
     * or subdirectory within the `ui5/` structure.
     *
     * Modules themselves are not directly routable or versioned as standalone resources,
     * but serve as the root context for all included artifacts.
     *
     * @see Ui5ModuleInterface
     */
    case Module = 0;

    /**
     * A standalone UI5 application with its own routing, Component.js, and entry point.
     * Typically served as a separate HTML page.
     *
     * @see Ui5AppInterface
     */
    case Application = 1;

    /**
     * A reusable UI5 library containing controls, formatters, helpers, or shared logic.
     * Can be imported by multiple applications.
     *
     * @see Ui5LibraryInterface
     */
    case Library = 2;

    /**
     * A UI5 Integration Card, such as ObjectCard, ListCard, or TableCard.
     * Usually configured via a manifest and rendered inside a dashboard or shell.
     *
     * @see Ui5CardInterface
     */
    case Card = 3;

    /**
     * A backend-powered report providing structured tabular or aggregated data.
     * Typically supports parameterization and optional export (e.g., CSV, Excel).
     *
     * @see Ui5ReportInterface
     */
    case Report = 4;

    /**
     * A Tile definition, representing a lightweight but versioned UI5 artifact.
     *
     * Tiles act as *UI composition units* within dashboards, launchpads, or other
     * shell containers. Each tile provides metadata such as title, description,
     * icon, and target, and may reference other artifacts (e.g., KPI, Report, App).
     *
     * Unlike Cards, Tiles are not intended for complex rendering or configuration.
     * They primarily serve as *entry points* or *compact visual summaries*,
     * often embedding KPIs or linking to other routable artifacts.
     *
     * Characteristics:
     * - Routable: No. Tiles are embedded only, never accessed via URL directly.
     * - Versionable: Yes. Tile layout, label, or KPI binding may change over time.
     * - Discoverable: Yes. Tiles are registered in the Ui5Registry.
     *
     * @see Ui5TileInterface
     */
    case Tile = 5;

    /**
     * A KPI (Key Performance Indicator) definition, representing a reusable
     * and versioned *data artifac* within the system.
     *
     * KPIs encapsulate metadata such as ID, title, unit, aggregation logic,
     * thresholds, and applicable contexts. They provide the data basis for
     * visualization in Tiles, Cards, or Dashboards.
     *
     * Characteristics:
     * - Routable: No. KPIs are data definitions, not standalone UI routes.
     * - Versionable: Yes. Thresholds, aggregation rules, or calculation logic
     *   may evolve over time.
     * - Reusable: Yes. The same KPI can appear in multiple Tiles, Cards, or
     *   Dashboards.
     * - Discoverable: Yes. KPIs are registered in the Ui5Registry.
     *
     * @see Ui5KpiInterface
     */
    case Kpi = 6;

    /**
     * A dashboard aggregating multiple Cards or Tiles in a layout.
     * May be statically defined (via manifest) or assembled dynamically.
     *
     * @see Ui5DashboardInterface
     */
    case Dashboard = 7;

    /**
     * A backend-bound action that can be triggered via direct API route.
     * UI5 Actions are invokable controller classes designed for stateless interaction,
     * such as toggling flags, triggering workflows, or executing custom business logic.
     *
     * Actions are uniquely addressable within their module via an HTTP method and route segment.
     *
     * @see Ui5ActionInterface
     */
    case Action = 8;

    /**
     * A Resource is a lightweight, read-only UI5 artifact that exposes
     * structured data for consumption by the client (e.g., cards, dashboards, lists).
     *
     * @see Ui5ResourceInterface
     */
    case Resource = 9;

    /**
     * A Ui5Dialog represents a globale invokable View/Controller pair bound to
     * a named URI segment.
     *
     * @see Ui5DialogInterface
     */
    case Dialog = 10;

    /**
     * Returns a human-readable label for the artifact type.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::Module => 'Module',
            self::Application => 'Application',
            self::Library => 'Library',
            self::Card => 'Card',
            self::Report => 'Report',
            self::Tile => 'Tile',
            self::Kpi => 'KPI',
            self::Dashboard => 'Dashboard',
            self::Action => 'Action',
            self::Resource => 'Resource',
            self::Dialog => 'Dialog',
        };
    }

    /**
     * Returns the type prefix for routable ArtifactTypes.
     *
     * @return string
     */
    public function routePrefix(): string
    {
        return match ($this) {
            self::Application => 'app',
            self::Library     => 'lib',
            self::Card        => 'card',
            self::Dashboard   => 'dashboard',
            self::Report      => 'report',
            self::Resource    => 'resource',
            self::Action      => 'api',
            default => throw new NonRoutableArtifactException($this->name)
        };
    }
}
