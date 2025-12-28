<?php

namespace Fixtures\Hello\Resources\First;

use LaravelUi5\Core\Ui5\AbstractDataProvider;

class Provider extends AbstractDataProvider
{
    public function execute(): array
    {
        return [
            'status' => 'Success',
            'message' => 'The resource was aggregated successfully.'
        ];
    }
}
