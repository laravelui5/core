<?php

namespace LaravelUi5\Core\Exceptions;

class MissingDefaultValueException extends Ui5Exception
{
    public function __construct(string $name)
    {
        parent::__construct(400, "Required setting `{$name}` has no default.");
    }
}
