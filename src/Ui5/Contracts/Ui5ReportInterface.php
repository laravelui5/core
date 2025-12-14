<?php

namespace LaravelUi5\Core\Ui5\Contracts;

/**
 * A discoverable UI5 Report Artifact that includes selection, result, and optional actions.
 *
 * This interface defines the required contract for a report to be integrated into the
 * UI5 Admin Panel and executed via the central ReportController.
 *
 * Each report artifact provides:
 * - A JavaScript namespace (used in UI5 client)
 * - A semantic version for cache busting
 * - Title, description, and a unique urlKey
 * - Access to the data provider logic
 * - View templates for selection and report output
 * - Optionally: follow-up actions
 */
interface Ui5ReportInterface extends Ui5ArtifactInterface, SluggableInterface, SlugSettableInterface
{
    /**
     * Returns the class that handles data retrieval and export logic.
     */
    public function getProvider(): DataProviderInterface;

    /**
     * Returns the path to the UI5 selection view Blade template.
     */
    public function getSelectionViewPath(): string;

    /**
     * Returns the path to the UI5 report result view Blade template.
     */
    public function getSelectionControllerPath(): string;

    /**
     * Returns the path to the Laravel Blade view used to render the final report output.
     *
     * This view receives the result from the DataProvider as `$data`,
     * and can render tables, charts, summaries or any other layout.
     */
    public function getReportView(): string;

    /**
     * Returns a list of available follow-up actions.
     *
     * @return array<string, ReportActionInterface> e.g. ['discard' => new Ui5AnnualCutOffCommitAction()]
     */
    public function getActions(): array;
}
