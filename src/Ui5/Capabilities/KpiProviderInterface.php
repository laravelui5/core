<?php

namespace LaravelUi5\Core\Ui5\Capabilities;

use LaravelUi5\Core\Enums\AggregationLevel;

/**
 * Contract for KPI data providers delivering live metric values.
 *
 * Implementations must return a valid associative array that represents
 * a complete KPI snapshot suitable for rendering inside UI5 dashboards.
 *
 * A provider receives the aggregation level (e.g., monthly, weekly) and
 * a set of context parameters (e.g. 'project_id' => 42) to produce the data.
 */
interface KpiProviderInterface
{
    /**
     * Returns the data payload for the KPI tile.
     *
     * The returned array should match the expected UI5 JSON structure,
     * typically including keys such as 'value', 'valueColor', 'indicator',
     * 'unit', 'scale', and optionally a trend or target.
     *
     * @param AggregationLevel $aggregationLevel  Level of data grouping (daily, weekly, etc.)
     * @param array<string, mixed> $context       Contextual filters like project ID, year, etc.
     *
     * @return array<string, mixed>               KPI payload as an associative array
     */
    public function toJson(AggregationLevel $aggregationLevel, array $context): array;
}
