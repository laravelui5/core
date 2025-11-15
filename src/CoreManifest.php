<?php

namespace LaravelUi5\Core;

use LaravelUi5\Core\Ui5\AbstractManifest;

class CoreManifest extends AbstractManifest
{

    protected function enhanceFragment(string $module): array
    {
        return [];
    }
}
