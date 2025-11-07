<?php

namespace Tests\Fixture\Hello\Errors\Ability\DoubledAbility;

use Tests\Fixture\Hello\HelloModule;

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
