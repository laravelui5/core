<?php

namespace LaravelUi5\Core\Ui5\Contracts;

use LaravelUi5\Core\Ui5\Capabilities\ResolvableInterface;

/**
 * Represents a UI5 Tile artifact that can be registered in the Ui5Registry
 * and rendered within a dashboard context.
 */
interface Ui5TileInterface extends Ui5ArtifactInterface, ResolvableInterface
{
}
