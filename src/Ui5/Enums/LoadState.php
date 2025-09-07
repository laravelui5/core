<?php

namespace LaravelUi5\Core\Ui5\Enums;

/**
 * Enumeration of possible load states for sap.m.TileContent and sap.m.GenericTile controls.
 * Used to represent and visualize the current status of data loading or error conditions.
 *
 * @see https://sdk.openui5.org/api/sap.m.LoadState
 */
enum LoadState: string
{
    /**
     * Content is currently loading. A busy indicator will be shown.
     */
    case Loading = 'Loading';

    /**
     * Content is successfully loaded and will be displayed.
     */
    case Loaded = 'Loaded';

    /**
     * Content loading failed. An error placeholder will be displayed.
     */
    case Failed = 'Failed';

    /**
     * Content is not available or intentionally disabled. An empty state will be shown.
     */
    case Disabled = 'Disabled';
}
