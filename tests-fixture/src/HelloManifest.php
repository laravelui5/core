<?php

namespace Fixtures\Hello;

use LaravelUi5\Core\Ui5\AbstractManifest;

class HelloManifest extends AbstractManifest
{
    protected function contributeFragment(string $module): array
    {
        return [];
    }
}
