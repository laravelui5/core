<?php

namespace LaravelUi5\Core\Exceptions;

class InvalidParameterValueException extends Ui5Exception
{
    public function __construct(string $name, string $type)
    {
        parent::__construct(400, "Invalid value for parameter `{$name}` (expected {$type})");
    }
}
