<?php

namespace LaravelUi5\Core\Exceptions;

class InvalidSettingException extends Ui5Exception
{
    public function __construct(string $message)
    {
        parent::__construct(500, $message);
    }
}
