<?php

namespace LaravelUi5\Core\Ui5\Contracts;

use LaravelUi5\Core\Enums\AggregationLevel;

/**
 * Contract for UI5 KPI tiles embedded in dashboards or standalone views.
 *
 * Each KPI defines its own metadata, aggregation level, unit and optional scale,
 * and references a data provider responsible for delivering dynamic content.
 *
 * KPIs are treated as first-class UI5 artifacts and must declare a unique
 * namespace and version. This enables them to be listed, routed and managed
 * like any other UI5 entity (App, Library, Card, etc.).
 */
interface Ui5KpiInterface extends Ui5ArtifactInterface, SluggableInterface
{
    /**
     * Returns the aggregation level (e.g. daily, weekly, monthly).
     *
     * Indicates how the underlying data is grouped.
     *
     * @return AggregationLevel
     */
    public function getAggregationLevel(): AggregationLevel;

    /**
     * Returns an array of context keys (e.g. 'project_id', 'year').
     *
     * These keys are expected as filters or parameters when requesting
     * dynamic data via the data provider.
     *
     * @return string[]
     */
    public function getContextKeys(): array;

    /**
     * Returns the data provider responsible for retrieving live KPI data.
     *
     * The provider will return the actual value, indicator, valueColor, etc.
     * and is invoked at runtime inside the dashboard view.
     *
     * @return KpiProviderInterface
     */
    public function getProvider(): KpiProviderInterface;
}
