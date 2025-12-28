<?php

namespace Fixtures\Hello\Actions\World;

use LaravelUi5\Core\Ui5\AbstractActionHandler;

class Handler extends AbstractActionHandler
{
    public function execute(): array
    {
        return [
            'status' => 'Success',
            'message' => 'The action was executed successfully.'
        ];
    }
}
