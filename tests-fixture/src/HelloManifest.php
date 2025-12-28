<?php

namespace Fixtures\Hello;

use LaravelUi5\Core\Ui5\AbstractManifest;

class HelloManifest extends AbstractManifest
{
    protected function enhanceFragment(string $module): array
    {
        return [];
    }
}
