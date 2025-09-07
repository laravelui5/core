<?php

namespace LaravelUi5\Core\Exceptions;

class OutdatedVersionException extends Ui5Exception
{
    public function __construct(string $app, string $requested, string $registered)
    {
        parent::__construct(410, "The current version of Ui5App `{$app}` is `{$registered}`. Requested version `{$requested}` is no longer supported.");
    }
}
