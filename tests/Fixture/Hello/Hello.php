<?php

namespace Tests\Fixture\Hello;

class Hello
{
    public const string SLUG = 'hello';
    public const string NAMESPACE = 'com.laravelui5.hello';
    public const string ROLE = 'Test';
    public const string ACTION_NAME = 'toggleLock';

    public static function ui5Config(): array
    {
        return [
            'modules' => [
                self::SLUG => \Tests\Fixture\Hello\HelloModule::class,
            ]
        ];
    }
}
