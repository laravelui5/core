<?php

namespace LaravelUi5\Core\Exceptions;

class InvalidParameterException extends Ui5Exception
{
    public function __construct(string $name)
    {
        parent::__construct(500, "Parameter `{$name}` of type MODEL requires a model class.");
    }
}
