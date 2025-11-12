<?php

namespace Tests\Fixture\Hello\Errors\Ability\DoubledAbility;

use LaravelUi5\Core\Attributes\Role;
use Tests\Fixture\Hello\Hello;
use Tests\Fixture\Hello\HelloModule;

#[Role(Hello::ROLE, 'Administrative access to Hello module')]
class Module extends HelloModule
{
    public function getActions(): array
    {
        return [
            new Action($this),
            new Action2($this),
        ];
    }
}
