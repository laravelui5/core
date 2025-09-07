<?php

namespace LaravelUi5\Core\Exceptions;

class MissingRequiredParameterException extends Ui5Exception
{
    public function __construct(string $name)
    {
        parent::__construct(400, "Missing required parameter `{$name}`");
    }
}
