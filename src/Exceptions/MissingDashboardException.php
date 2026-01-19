<?php

namespace LaravelUi5\Core\Exceptions;

class MissingDashboardException extends Ui5Exception
{
    public function __construct(string $path)
    {
        parent::__construct(404, "Dashboard component could not be found at `{$path}`");
    }
}
