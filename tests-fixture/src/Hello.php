<?php

namespace Fixtures\Hello;

class Hello
{
    public const string SLUG = 'hello';
    public const string NAMESPACE = 'com.laravelui5.hello';
    public const string ROLE = 'Test';
    public const string ACTION_NAME = 'toggleLock';
    public const string ACTION_SLUG = 'com.laravelui5.hello.actions.world';

    public static function ui5Config(): array
    {
        return [
            'modules' => [
                \Fixtures\Hello\HelloModule::class,
            ]
        ];
    }
}
