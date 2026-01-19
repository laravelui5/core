<?php

namespace LaravelUi5\Core\Exceptions;

class MissingManifestException extends Ui5Exception
{
    public function __construct(string $path)
    {
        parent::__construct(404, "No manifest.json could be found at `$path`");
    }
}
