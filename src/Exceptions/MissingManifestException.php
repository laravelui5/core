<?php

namespace LaravelUi5\Core\Exceptions;

class MissingManifestException extends Ui5Exception
{
    public function __construct(string $manifestPath)
    {
        parent::__construct(404, "No manifest.json could be found at `$manifestPath`");
    }
}
