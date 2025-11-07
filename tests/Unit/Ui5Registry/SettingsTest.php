<?php

use LaravelUi5\Core\Ui5\Ui5Registry;
use Tests\Fixture\Hello\Hello;

describe('Settings discovery', function () {
    it('discovers settings attributes correctly', function () {
        $registry = Ui5Registry::fromArray(Hello::ui5Config());
        $settings = $registry->settings();

        expect($settings)
            ->toBeArray()
            ->toHaveKey(Hello::NAMESPACE)
            ->and($settings[Hello::NAMESPACE])
            ->toHaveKey('darkMode')
            ->toHaveKey('maxItems')
            ->and($settings[Hello::NAMESPACE]['darkMode'])
            ->toMatchArray([
                'type' => 'Boolean',
                'scope' => 'User',
                'default' => false,
                'visibilityRole' => 'Employee',
            ]);
    });

    it('throws when Setting with same name per namespace', function () {
        expect(fn() => Ui5Registry::fromArray([
            'modules' => [
                'foo' => \Tests\Fixture\Hello\Errors\Settings\DuplicateSettingModule::class,
            ]
        ]))->toThrow(LogicException::class, 'Duplicate setting');
    });
});
