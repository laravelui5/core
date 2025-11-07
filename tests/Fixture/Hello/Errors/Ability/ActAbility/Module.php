<?php

namespace Tests\Fixture\Hello\Errors\Ability\ActAbility;

use Tests\Fixture\Hello\HelloModule;

class Module extends HelloModule
{
    public function getResources(): array
    {
        return [
            new Resource($this),
        ];
    }
}
