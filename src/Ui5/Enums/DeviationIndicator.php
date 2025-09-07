<?php

namespace LaravelUi5\Core\Ui5\Enums;

/**
 * Enum of the available deviation markers for the NumericContent control.
 *
 * @see https://sdk.openui5.org/api/sap.m.DeviationIndicator
 */
enum DeviationIndicator: string
{

    /**
     * The actual value is less than the target value.
     */
    case Down = 'Down';

    /**
     * No value (change).
     */
    case None = 'None';

    /**
     * The actual value is more than the target value.
     */
    case Up = 'Up';
}
