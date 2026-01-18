<?php

namespace LaravelUi5\Core\Exceptions;

class UndefinedEndpointException extends Ui5Exception
{
    public function __construct($key)
    {
        parent::__construct(400, "Artifact '{$key}' does not expose an OData endpoint.");
    }
}
