<?php

namespace LaravelUi5\Core\Exceptions;

class MissingArtifactException extends Ui5Exception
{
    public function __construct(string $urlKey)
    {
        parent::__construct(404, "No artifact found for `$urlKey`");
    }
}
