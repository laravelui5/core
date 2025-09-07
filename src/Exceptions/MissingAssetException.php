<?php

namespace LaravelUi5\Core\Exceptions;

class MissingAssetException extends Ui5Exception
{
    public function __construct(string $type, string $slug, string $version, string $file)
    {
        parent::__construct(404, "Asset not found: {$type}/{$slug}/{$version}/{$file}");
    }
}
