<?php

namespace Tests\Fixture\Hello;

use LaravelUi5\Core\Ui5\AbstractLaravelUi5Manifest;

class HelloManifest extends AbstractLaravelUi5Manifest
{
    protected function enhanceFragment(string $module): array
    {
        return [];
    }
}
