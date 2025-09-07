<?php

namespace LaravelUi5\Core\Exceptions;

class InvalidReportActionException extends Ui5Exception
{
    public function __construct(string $name)
    {
        parent::__construct(500, "Action `{$name}` does not implement ReportActionInterface.");
    }
}
