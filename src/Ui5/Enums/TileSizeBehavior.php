<?php

namespace LaravelUi5\Core\Ui5\Enums;

/**
 * Describes the behavior of tiles when displayed on a small-screened phone (374px wide and lower).
 *
 * @see https://sdk.openui5.org/api/sap.m.TileSizeBehavior
 */
enum TileSizeBehavior: string
{

    /**
     * Default behavior: Tiles adapt to the size of the screen, getting smaller on small screens.
     */
    case Responsive = 'Responsive';

    /**
     * Tiles are small all the time, regardless of the actual screen size.
     */
    case Small = 'Small';
}
