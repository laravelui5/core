<?php

namespace LaravelUi5\Core\Exceptions;

class InvalidPathException extends Ui5Exception
{
    public function __construct(string $path)
    {
        parent::__construct(400, "Invalid ui5 path provided `{$path}`");
    }
}
