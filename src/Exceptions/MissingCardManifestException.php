<?php

namespace LaravelUi5\Core\Exceptions;

class MissingCardManifestException extends Ui5Exception
{
    public function __construct(string $manifestPath)
    {
        parent::__construct(404, "Card manifest could not be found at `{$manifestPath}`");
    }
}
