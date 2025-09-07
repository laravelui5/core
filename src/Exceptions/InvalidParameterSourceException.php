<?php

namespace LaravelUi5\Core\Exceptions;

class InvalidParameterSourceException extends Ui5Exception
{
    public function __construct(string $name, string $source)
    {
        parent::__construct(400, "Invalid parameter source `{$source}` for parameter `{$name}`");
    }
}
