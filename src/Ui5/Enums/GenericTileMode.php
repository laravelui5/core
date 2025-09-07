<?php

namespace LaravelUi5\Core\Ui5\Enums;

/**
 * Defines the mode of GenericTile.
 *
 * @see https://sdk.openui5.org/api/sap.m.GenericTileMode
 */
enum GenericTileMode: string
{

    /**
     * Action Mode (Two lines for the header).
     *
     * Generic Tile renders buttons that are specified under 'actionButtons' aggregation
     */
    case ActionMode = 'ActionMode';

    /**
     * Article Mode (Two lines for the header and one line for the subtitle).
     *
     * Enables Article Mode.
     */
    case ArticleMode = 'ArticleMode';

    /**
     * Default mode (Two lines for the header and one line for the subtitle).
     */
    case ContentMode = 'ContentMode';

    /**
     * Header mode (Four lines for the header and one line for the subtitle).
     */
    case HeaderMode = 'HeaderMode';

    /**
     * Icon mode.
     *
     * GenericTile displays a combination of icon and header title.
     *
     * It is applicable only for the OneByOne FrameType and TwoByHalf FrameType.
     */
    case IconMode  = 'IconMode';

    /**
     * Line mode (Implemented for both, cozy and compact densities).
     *
     * Generic Tile is displayed as in-line element, header and subheader are
     * displayed in one line. In case the texts need more than one line, the
     * representation depends on the used density. Cozy: The text will be
     * truncated and the full text is shown in a tooltip as soon as the tile
     * is hovered (desktop only). Compact: Header and subheader are rendered
     * continuously spanning multiple lines, no tooltip is provided).
     */
    case LineMode = 'LineMode';
}
