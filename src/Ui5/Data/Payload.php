<?php

namespace LaravelUi5\Core\Ui5\Data;

/**
 * Base data object for any renderable UI5 element.
 *
 * This class acts as a neutral starting point for UI components (Tiles, Cards, etc.)
 * that require input data for rendering. To implement a concrete renderable,
 * extend this class and provide the required properties via constructor.
 *
 * Example:
 * <code>
 * class MyData extends Payload {
 *     public function __construct(
 *         public readonly int $value,
 *         public readonly ValueColor $color,
 *     ) {
 *         parent::__construct();
 *     }
 * }
 * </code>
 */
readonly class Payload
{
    public function __construct()
    {
    }
}
