<?php

namespace LaravelUi5\Core\Ui5\Contracts;

use Flat3\Lodata\EntityType;

/**
 * Contract for the actual data provider behind a UI5 Report.
 *
 * A ReportDataProvider is responsible for executing queries, computing results,
 * and optionally preparing export data for Excel or CSV output.
 *
 * This interface is intentionally slim and focused on runtime logic only.
 * Artifact-related metadata belongs into Ui5ReportInterface.
 */
interface ReportDataProviderInterface extends DataProviderInterface
{
    /**
     * Returns the entity type definition used for this report.
     *
     * The entity type defines the available properties (columns), their data types,
     * and optionally, labels or formatting hints. It is the central metadata source
     * for UI rendering (e.g., table columns, filter fields) and export logic.
     *
     * Example usage:
     * - sap.ui.mdc.Table column binding
     * - PDF/Excel column configuration
     * - automatic $metadata generation via Lodata
     *
     * @return EntityType|null
     */
    public function getEntityType(): ?EntityType;

    /**
     * Optional: Returns a data stream for Excel/CSV export.
     *
     * If this method is not implemented, export is considered unsupported.
     * The format can be a Laravel Excel object, generator(), or a pure array.
     *
     * @param array $context
     * @return mixed|null
     */
    public function getExportData(array $context): mixed;

    /**
     * Returns the name of the file when downloaded by the user.
     *
     * Example: 'Jahresabgrenzung_2025.pdf'
     */
    public function getReportName(): string;
}
