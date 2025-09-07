<?php

namespace LaravelUi5\Core\Enums;

use LaravelUi5\Core\Exceptions\InvalidPathException;
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
        };
    }

    /**
     * Returns the composed URL key (slug path) for a given artifact.
     *
     * The URL key determines the routing path used by the UI5 frontend and Laravel
     * backend to locate and invoke this artifact. It forms the core part of the
     * dynamic route segment that appears in URLs like:
     *
     *     /ui5/app/{slug}/{version}
     *     /ui5/card/{module}/{slug}
     *     /ui5/api/{module}/{action}
     *
     * This method mirrors the routing conventions defined in `routes/ui5.php`
     * and must remain in sync with the corresponding Route::pattern definitions.
     *
     * In cases where no slug is required (e.g., Applications or Libraries),
     * the returned path includes only the module-level key (e.g. "app/core").
     *
     * In cases where the artifact is not addressable via URL (e.g., abstract types),
     * this method returns `null`.
     *
     * @param Ui5ArtifactInterface $artifact The artifact to generate the URL key for
     * @return string|null URL key (e.g. "card/core/budget") or null if an artifact type is not routable
     */
    public static function urlKeyFromArtifact(Ui5ArtifactInterface $artifact): ?string
    {
        return match ($artifact->getType()) {
            self::Application => "app/{$artifact->getModule()->getSlug()}",
            self::Library => "lib/{$artifact->getModule()->getSlug()}",
            self::Card => "card/{$artifact->getModule()->getSlug()}/{$artifact->getSlug()}",
            self::Report => "report/{$artifact->getModule()->getSlug()}/{$artifact->getSlug()}",
            self::Action => "api/{$artifact->getModule()->getSlug()}/{$artifact->getSlug()}",
            self::Resource => "resource/{$artifact->getModule()->getSlug()}/{$artifact->getSlug()}",
            self::Dashboard => "dashboard/{$artifact->getSlug()}",
            self::Module, self::Tile, self::Kpi => null,
        };
    }

    /**
     * Resolve a UI5 artifact urlKey from a request path.
     *
     * This method inspects the path relative to the configured UI5 route prefix
     * and attempts to construct a valid urlKey suitable for Ui5Registry lookup.
     *
     * Expected formats:
     * - app/{module}
     * - lib/{module}
     * - card/{module}/{slug}
     * - dashboard/{slug}
     * - report/{module}.{slug}
     * - api/{module}/{slug}
     * - resource/{module}/{slug}
     *
     * Behavior:
     * - Returns the normalized urlKey string (e.g. "card/foo/bar").
     * - Aborts with HTTP 400 if the path matches a UI5 artifact type
     *   but is incomplete or malformed.
     * - Aborts with HTTP 400 if the path has fewer than two parts
     *   or does not map to a known artifact type.
     *
     * @param string $path Relative request path below the UI5 route prefix
     * @return string Non-null urlKey for Ui5Registry lookup
     */
    public static function urlKeyFromPath(string $path): string
    {
        $parts = explode('/', trim($path, '/'));
        if (count($parts) < 2) {
            throw new InvalidPathException($path);
        }
        $key = match ($parts[0]) {
            'app' => "app/" . $parts[1],
            'lib' => "lib/" . $parts[1],
            'dashboard' => "dashboard/" . $parts[1],
            'card' => isset($parts[2])
                ? "card/" . $parts[1] . "/" . $parts[2]
                : null,
            'report' => isset($parts[2])
                ? "report/" . $parts[1] . "/" . $parts[2]
                : null,
            'api' => isset($parts[2])
                ? "api/" . $parts[1] . "/" . $parts[2]
                : null,
            'resource' => isset($parts[2])
                ? "resource/" . $parts[1] . "/" . $parts[2]
                : null,
            default => null,
        };

        if (null === $key) {
            throw new InvalidPathException($path);
        }
        return $key;
    }
}
