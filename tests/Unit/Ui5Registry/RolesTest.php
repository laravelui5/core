<?php

use LaravelUi5\Core\Ui5\Ui5Registry;
use Tests\Fixture\Hello\Hello;

describe('Introspection Layer — Roles', function () {
    it('discovers roles declared via attribute', function () {
        $registry = Ui5Registry::fromArray(Hello::ui5Config());
        $roles = $registry->roles();

        expect($roles)
            ->toBeArray()
            ->toHaveKey(Hello::ROLE)
            ->and($roles[Hello::ROLE])->toContain('Administrative access');
    });

    it('throws on duplicate role declarations', function () {
        // Fake second module with same role to trigger duplicate error
        $config = Hello::ui5Config();
        $config['modules']['duplicate'] = \Tests\Fixture\Hello\HelloModule::class;

        $test = fn() => Ui5Registry::fromArray($config);
        expect($test)->toThrow(LogicException::class, 'Role');
    });
});
