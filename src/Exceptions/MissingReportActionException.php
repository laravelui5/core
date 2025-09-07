<?php

namespace LaravelUi5\Core\Exceptions;

class MissingReportActionException extends Ui5Exception
{
    public function __construct(string $name, string $report)
    {
        parent::__construct(404, "Action `{$name}` does not exist for report `{$report}`.");
    }
}
