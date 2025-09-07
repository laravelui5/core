<?php

namespace LaravelUi5\Core\Ui5\Enums;

/**
 * Defines the scopes of GenericTile enabling the developer to implement different "flavors" of tiles.
 *
 * @see https://sdk.openui5.org/api/sap.m.GenericTileScope
 */
enum GenericTileScope: string
{
    /**
     * More action scope (Only the More icon is added to the tile)
     */
    case ActionMore = 'ActionMore';

    /**
     * Remove action scope (Only the Remove icon is added to the tile)
     */
    case ActionRemove = 'ActionRemove';

    /**
     * Action scope (Possible footer and Error State information is overlaid,
     * "Remove" and "More" icons are added to the tile).
     */
    case Actions = 'Actions';

    /**
     * Default scope (The default scope of the tile, no action icons are rendered).
     */
    case Display = 'Display';
}
