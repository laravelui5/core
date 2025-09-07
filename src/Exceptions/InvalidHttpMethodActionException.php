<?php

namespace LaravelUi5\Core\Exceptions;

class InvalidHttpMethodActionException extends Ui5Exception
{
    public function __construct(string $name, string $method)
    {
        parent::__construct(500, "Invalid HTTP method `{$method}` for Ui5Action `{$name}`. Expected one of [POST, PATCH, DELETE].");
    }
}
