<?php

namespace LaravelUi5\Core\Ui5\Data;

use LaravelUi5\Core\Ui5\Enums\DeviationIndicator;
use LaravelUi5\Core\Ui5\Enums\LoadState;
use LaravelUi5\Core\Ui5\Enums\ValueColor;

readonly class NumericContentData extends Payload
{
    /**
     * @param float|int|string|null $value the main numeric or textual value to be displayed in the tile content.
     * @param ValueColor|null $valueColor the semantic color for the value display (e.g., Good, Error).
     * @param DeviationIndicator|null $indicator the trend indicator for the value (e.g., "Up", "Down", "None").
     * @param string|null $scale the scale suffix to be displayed next to the value (e.g., "k", "€").
     * @param LoadState|null $state the loading or result state of the tile (e.g., Loaded, Failed).
     */
    public function __construct(
        public float|int|string|null $value = null,
        public ?ValueColor           $valueColor = null,
        public ?DeviationIndicator   $indicator = null,
        public ?string               $scale = null,
        public ?LoadState            $state = null,
    )
    {
        parent::__construct();
    }
}
