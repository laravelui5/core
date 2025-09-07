<?php

namespace LaravelUi5\Core\Exceptions;

class NoModelFoundForParameterException extends Ui5Exception
{
    public function __construct(string $name, string $modelClass)
    {
        parent::__construct(400, "Model `{$modelClass}` not found for parameter `{$name}`");
    }
}
