<?php

namespace LaravelUi5\Core\Exceptions;

class InvalidJsonParameterException extends Ui5Exception
{
    public function __construct(string $name)
    {
        parent::__construct(500, "Invalid JSON for array parameter `{$name}`.");
    }
}
