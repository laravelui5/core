<?php

namespace LaravelUi5\Core;

use LaravelUi5\Core\Ui5\AbstractManifest;

class CoreManifest extends AbstractManifest
{

    protected function contributeFragment(string $module): array
    {
        return [];
    }
}
