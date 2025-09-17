<?php

namespace LaravelUi5\Core\Ui5\Contracts;

use Flat3\Lodata\EntityType;

/**
 * Marker interface for DataProviders that expose metadata for
 * automatic UI generation (e.g., selection masks, table columns).
 *
 * If a provider implements this interface, the SDK can render
 * a generic UI5 selection mask based on the EntityType definition.
 */
interface MetadataAwareInterface
{
    /**
     * Returns the entity type definition used for this report.
     *
     * The entity type defines the available properties (columns),
     * their data types, and optionally labels or formatting hints.
     */
    public function getEntityType(): EntityType;
}
