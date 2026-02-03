<?php

namespace Fixtures\Hello\Actions\World;

use Fixtures\Hello\Models\User;
use LaravelUi5\Core\Attributes\Parameter;
use LaravelUi5\Core\Enums\ParameterType;
use LaravelUi5\Core\Ui5\Capabilities\ActionHandlerInterface;

#[Parameter(
    name: 'user',
    uriKey: 'user',
    type: ParameterType::Model,
    model: User::class
)]
class UserHandler implements ActionHandlerInterface
{
    public function handle(): array
    {
        return [
            'status' => 'Success',
            'message' => 'The action was executed successfully.'
        ];
    }
}
