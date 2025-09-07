<?php

namespace LaravelUi5\Core\Traits;

use Illuminate\Support\Facades\File;

/**
 * Default implementation for resolving UI5 asset paths within a module.
 *
 * Assumes assets are stored under `../resources/ui5/` relative to the implementing class.
 */
trait HasAssetsTrait
{
    public function getAssetPath(string $filename): ?string
    {
        $base = (new \ReflectionClass($this))->getFileName();
        $path = dirname($base) . '/../resources/ui5/' . ltrim($filename, '/');
        return File::exists($path) ? $path : null;
    }
}
