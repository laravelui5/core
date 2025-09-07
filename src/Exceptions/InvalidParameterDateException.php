<?php

namespace LaravelUi5\Core\Exceptions;

class InvalidParameterDateException extends Ui5Exception
{
    public function __construct(string $name)
    {
        parent::__construct(400, "Invalid date for parameter `{$name}`");
    }
}
