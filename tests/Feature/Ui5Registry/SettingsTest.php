<?php

use Fixtures\Hello\DuplicateSettingModule;
use Fixtures\Hello\Hello;
use LaravelUi5\Core\Enums\SettingScope;
use LaravelUi5\Core\Enums\SettingVisibilityRole;
use LaravelUi5\Core\Enums\SettingType;
use LaravelUi5\Core\Ui5\Ui5Registry;

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
                'type' => SettingType::Boolean,
                'scope' => SettingScope::User,
                'default' => false,
                'role' => SettingVisibilityRole::Employee,
                'note' => 'Something'
            ]);
    });

    it('throws when Setting with same name per namespace', function () {
        expect(fn() => Ui5Registry::fromArray([
            'modules' => [
                'hello' => DuplicateSettingModule::class,
            ]
        ]))->toThrow(LogicException::class, 'Duplicate setting');
    });
});
