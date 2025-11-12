<?php

namespace Tests\Fixture\Hello\Errors\Ability\ActAbility;

use LaravelUi5\Core\Attributes\Role;
use Tests\Fixture\Hello\Hello;
use Tests\Fixture\Hello\HelloModule;

#[Role(Hello::ROLE, 'Administrative access to Hello module')]
class Module extends HelloModule
{
    public function getResources(): array
    {
        return [
            new Resource($this),
        ];
    }
}
