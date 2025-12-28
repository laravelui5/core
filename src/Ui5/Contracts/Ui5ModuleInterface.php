<?php

namespace LaravelUi5\Core\Ui5\Contracts;

use LaravelUi5\Core\Contracts\Ui5Source;

/**
 * A Ui5Module represents the root-level container for all UI5-related artifacts
 * provided by a feature package or domain module in the LaravelUi5 ecosystem.
 *
 * A module can contain either a UI5 application or a UI5 library, but not both.
 * Only applications can provide subordinate artifacts such as cards, actions,
 * reports, KPIs, and tiles.
 *
 * Responsibilities:
 * - Expose the main artifact (App or Library) via type-specific getters
 * - Declare all subordinate artifacts (if applicable)
 * - Provide artifact metadata via Ui5ArtifactInterface
 *
 * System rules:
 * - A module MUST provide either an app or a library (exclusive)
 * - A library MUST NOT define subordinate artifacts
 * - Every artifact must have a globally unique namespace
 */
interface Ui5ModuleInterface extends SluggableInterface
{
    /**
     * Returns the canonical name of the module.
     *
     * The module name is a human-readable identifier derived from the module’s
     * directory structure (e.g. `Timesheet`, `Partners`, `Finance`) and is used
     * for:
     *  - module introspection,
     *  - help/documentation resolution,
     *  - manifest generation,
     *  - CLI tooling,
     *  - and internal module mapping.
     *
     * The name must represent the top-level directory under `/ui5/<Module>/`,
     * and is therefore expected to be unique within the installation.
     *
     * @return string The canonical module name.
     */
    public function getName(): string;

    /**
     * Returns the absolute filesystem path to the UI5 source artifacts of this module.
     *
     * The returned path MUST resolve to a directory that contains the canonical
     * UI5 source artifacts of the module, either:
     *
     * - a development workspace (e.g. a locally linked UI5 project), or
     * - a packaged distribution shipped with the module (e.g. under `vendor/`).
     *
     * The concrete origin (workspace vs. package) is resolved by the registry at
     * runtime and MUST be transparent to consumers.
     *
     * This path is intended to serve as the single source of truth for all UI5
     * introspection and MUST remain stable for the lifetime of the application
     * instance.
     *
     * @return string Absolute path to the module’s UI5 source directory.
     */
    public function getSourcePath(): string;

    /**
     * Returns true if this module provides a UI5 application.
     *
     * @return bool
     */
    public function hasApp(): bool;

    /**
     * Returns the application artifact, if present.
     *
     * @return Ui5AppInterface|null
     */
    public function getApp(): ?Ui5AppInterface;

    /**
     * Returns true if this module provides a UI5 library.
     *
     * @return bool
     */
    public function hasLibrary(): bool;

    /**
     * Returns the library artifact, if present.
     *
     * @return Ui5LibraryInterface|null
     */
    public function getLibrary(): ?Ui5LibraryInterface;

    /**
     * Returns the root artifact of the module — either the application or the library,
     * depending on the module type.
     *
     * This method allows consumers to access the primary artifact in a generic way,
     * without checking whether the module is app- or library-based.
     *
     * @return Ui5ArtifactInterface The root artifact (App or Library)
     */
    public function getArtifactRoot(): Ui5ArtifactInterface;

    /**
     * Returns an array of all cards provided by this module.
     * Only available if this module provides an app.
     *
     * @return Ui5CardInterface[]
     */
    public function getCards(): array;

    /**
     * Returns an array of all KPIs provided by this module.
     *
     * @return Ui5KpiInterface[]
     */
    public function getKpis(): array;

    /**
     * Returns an array of all tiles for launchpad or navigation purposes.
     *
     * @return Ui5TileInterface[]
     */
    public function getTiles(): array;

    /**
     * Returns an array of all actions (API endpoints) provided by this module.
     *
     * @return Ui5ActionInterface[]
     */
    public function getActions(): array;

    /**
     * Returns an array of all resources (API) provided by this module.
     *
     * @return Ui5ResourceInterface[]
     */
    public function getResources(): array;

    /**
     * Returns an array of all dashboards (API) provided by this module.
     *
     * This artifact type is not discovered automatically. It must be declared
     * under the dedicated configuration key `dashboards` inside your module’s
     * `config/ui5.php`.
     *
     * @return Ui5DashboardInterface[]
     */
    public function getDashboards(): array;

    /**
     * Returns an array of all resports (API) provided by this module.
     *
     * This artifact type is not discovered automatically. It must be declared
     * under the dedicated configuration key `reports` inside your module’s
     * `config/ui5.php`.
     *
     * @return Ui5ReportInterface[]
     */
    public function getReports(): array;

    /**
     * Returns an array of all dialogs (API) provided by this module.
     *
     * This artifact type is not discovered automatically. It must be declared
     * under the dedicated configuration key `dialogs` inside your module’s
     * `config/ui5.php`.
     *
     * @return Ui5DialogInterface[]
     */
    public function getDialogs(): array;

    /**
     * Returns all artifacts belonging to this module (app, library, tiles, cards,
     * kpis, actions, resources, dashboards, reports, dialogs, etc.)
     *
     * @return Ui5ArtifactInterface[]
     */
    public function getAllArtifacts(): array;
}
