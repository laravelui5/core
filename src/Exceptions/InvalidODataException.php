<?php

namespace LaravelUi5\Core\Exceptions;

class InvalidODataException extends Ui5Exception
{
    public function __construct()
    {
        parent::__construct(400, 'Invalid OData route. Missing namespace or version.');
    }
}
