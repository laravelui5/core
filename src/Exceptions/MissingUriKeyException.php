<?php

namespace LaravelUi5\Core\Exceptions;

class MissingUriKeyException extends Ui5Exception
{
    public function __construct(string $name)
    {
        parent::__construct(400, "Missing uriKey for parameter `{$name}`");
    }
}
