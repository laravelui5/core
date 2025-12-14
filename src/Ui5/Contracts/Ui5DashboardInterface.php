<?php

namespace LaravelUi5\Core\Ui5\Contracts;

/**
 * Represents a UI5 Dashboard artifact that aggregates Tiles and Cards into a structured layout.
 *
 * Dashboards serve as entry points for visualizing business contexts and overviews,
 * often composed of reusable UI5 elements. Each dashboard is associated with a static
 * or dynamic XML fragment that defines the layout structure in a format consumable by
 * the UI5 runtime (e.g., sap.ui.core.Fragment).
 *
 * Implementations are responsible for providing a valid XML blade path which can be rendered
 * and injected into the SAPUI5 shell container or application shell.
 */
interface Ui5DashboardInterface extends Ui5ArtifactInterface, SluggableInterface, SlugSettableInterface
{
    /**
     * Returns the absolute or resource-relative path to the dashboard Blade view.
     *
     * This view is expected to contain an XML fragment definition, typically using the
     * <core:FragmentDefinition> root element and appropriate UI5 XML namespaces.
     *
     * @return string Path to the XML blade template.
     */
    public function getDashboard(): string;
}
