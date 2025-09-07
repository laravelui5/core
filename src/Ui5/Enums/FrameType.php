<?php

namespace LaravelUi5\Core\Ui5\Enums;

/**
 * Enum for possible frame size types for sap.m.TileContent and sap.m.GenericTile control.
 *
 * @see https://sdk.openui5.org/api/sap.m.FrameType
 */
enum FrameType: string
{
    /**
     * The Auto frame type that adjusts the size of the control to the content.
     * Support for this type in sap.m.GenericTile is deprecated since 1.48.0.
     *
     * Corresponds to: FrameType.Auto
     */
    case Auto = 'Auto';

    /**
     * The 2x1 frame type. Note: The 2x1 frame type is currently only supported for Generic tile.
     */
    case OneByHalf = 'OneByHalf';

    /**
     * The 2x2 frame type.
     */
    case OneByOne = 'OneByOne';

    /**
     * The 4x1 frame type. Note: The 4x1 frame type is currently only supported for Generic tile.
     */
    case TwoByHalf = 'TwoByHalf';

    /**
     * The 4x2 frame type.
     */
    case TwoByOne = 'TwoByOne';
}
