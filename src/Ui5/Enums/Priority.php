<?php

namespace LaravelUi5\Core\Ui5\Enums;

/**
 * Defines the priority for the TileContent in ActionMode.
 *
 * @see https://sdk.openui5.org/api/sap.m.Priority
 */
enum Priority: string
{

    /**
     * It displays high priority color for the GenericTag
     */
    case High = 'High';

    /**
     * It displays low priority color for the GenericTag
     */
    case Low = 'Low';

    /**
     * It displays medium priority color for the GenericTag
     */
    case Medium = 'Medium';

    /**
     * The priority is not set
     */
    case None = 'None';

    /**
     * It displays very high priority color for the GenericTag
     */
    case VeryHigh = 'VeryHigh';
}
