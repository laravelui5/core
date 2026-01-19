<?php

namespace LaravelUi5\Core\Exceptions;

class MissingCardManifestException extends Ui5Exception
{
    public function __construct(string $path)
    {
        parent::__construct(404, "Card manifest could not be found at `{$path}`");
    }
}
