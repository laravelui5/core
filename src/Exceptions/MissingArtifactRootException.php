<?php

namespace LaravelUi5\Core\Exceptions;

class MissingArtifactRootException extends Ui5Exception
{
    public function __construct(string $module)
    {
        parent::__construct(500, "Missing artifact root for module $module");
    }
}
