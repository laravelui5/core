<?php

namespace LaravelUi5\Core\Exceptions;

class UndefinedBagKeyException extends Ui5Exception
{
    public function __construct(string $key)
    {
        parent::__construct(500, "Undefined key `{$key}`.");
    }
}
