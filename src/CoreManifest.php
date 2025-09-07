<?php

namespace LaravelUi5\Core;

use LaravelUi5\Core\Ui5\AbstractLaravelUi5Manifest;

class CoreManifest extends AbstractLaravelUi5Manifest
{

    protected function enhanceFragment(string $module): array
    {
        return [];
    }
}
