<?php

namespace LaravelUi5\Core\Exceptions;

class NonRoutableArtifactException extends Ui5Exception
{
    public function __construct(string $type)
    {
        parent::__construct("Artifact of type `$type` is not routable.");
    }
}
