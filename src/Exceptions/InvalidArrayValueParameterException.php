<?php

namespace LaravelUi5\Core\Exceptions;

class InvalidArrayValueParameterException extends Ui5Exception
{
    public function __construct(string $name)
    {
        parent::__construct(500, "Invalid element in array for parameter `{$name}`.");
    }
}
