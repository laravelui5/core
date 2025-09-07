<?php

namespace LaravelUi5\Core\Exceptions;

class InvalidParameterTypeException extends Ui5Exception
{
    public function __construct(string $name)
    {
        parent::__construct(500, "Invalid parameter type for parameter `{$name}`.");
    }
}
