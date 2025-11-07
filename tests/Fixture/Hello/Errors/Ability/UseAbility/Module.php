<?php

namespace Tests\Fixture\Hello\Errors\Ability\UseAbility;

use Tests\Fixture\Hello\HelloModule;

class Module extends HelloModule
{
    public function getActions(): array
    {
        return [
            new Action($this),
        ];
    }
}
