<?php

use Fixtures\Hello\HelloLibModule;
use Fixtures\Hello\HelloModule;

return [
    'modules' => [
        HelloModule::class => '/../tests-fixture/ui5-hello',
        HelloLibModule::class => '/../tests-fixture/ui5-hello-lib',
    ]
];
