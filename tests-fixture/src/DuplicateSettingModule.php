<?php

namespace Fixtures\Hello;

use Fixtures\Hello\Errors\Settings\Action;

class DuplicateSettingModule extends HelloModule
{

    public function getActions(): array
    {
        return [
            new \Fixtures\Hello\Actions\World\Action($this),
            new Action($this)
        ];
    }

    public function getName(): string
    {
        return 'DuplicateSetting';
    }
}
