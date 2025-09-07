<?php

namespace LaravelUi5\Core\Ui5\Enums;

/**
 * Enumeration of possible value color settings.
 *
 * Used in sap.m.NumericContent and other tile-related controls to indicate
 * the semantic meaning or status of a value (e.g., positive, negative, critical).
 *
 * @see https://sdk.openui5.org/api/sap.m.ValueColor
 */
enum ValueColor: string
{

    /**
     * Critical color. Indicates a borderline or attention-required value (typically orange).
     */
    case Critical = 'Critical';

    /**
     * Error color. Indicates a problematic or unhealthy value or trend (typically red).
     */
    case Error = 'Error';

    /**
     * Good color. Indicates a positive or healthy value or trend (typically green).
     */
    case Good = 'Good';

    /**
     * Neutral color (default). Indicates a value without specific semantic meaning.
     */
    case Neutral = 'Neutral';

    /**
     * Good color. Indicates a positive or healthy value or trend (typically green).
     */
    case None = 'None';
}
