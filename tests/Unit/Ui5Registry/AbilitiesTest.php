<?php

use LaravelUi5\Core\Enums\AbilityType;
use LaravelUi5\Core\Ui5\Ui5Registry;
use Tests\Fixture\Hello\Hello;

describe('Introspection Layer — Abilities', function () {
    it('discovers backend Ability of type Act', function () {
        $registry = Ui5Registry::fromArray(Hello::ui5Config());
        $abilities = $registry->abilities();

        expect($abilities)
            ->toBeArray()
            ->toHaveKey(Hello::NAMESPACE)
            ->and($abilities[Hello::NAMESPACE])
            ->toHaveKey(AbilityType::Act->label())
            ->and($abilities[Hello::NAMESPACE][AbilityType::Act->label()])
            ->toHaveKey(Hello::ACTION_NAME)
            ->and($abilities[Hello::NAMESPACE][AbilityType::Act->label()][Hello::ACTION_NAME])
            ->toMatchArray([
                'type' => AbilityType::Act,
                'role' => 'Admin',
                'note' => 'Lock or unlock a record',
            ]);
    });

    it('does not allow multiple Ability attributes per class (PHP-level)', function () {
        expect(true)->toBeTrue(); // dokumentarischer Platzhalter
    })->skip('PHP natively prevents multiple non-repeatable attributes.');

    it('throws when Ability type USE is declared on backend artifact', function () {
        expect(fn() => Ui5Registry::fromArray([
            'modules' => [
                'foo' => \Tests\Fixture\Hello\Errors\Ability\UseAbility\Module::class,
            ]
        ]))->toThrow(LogicException::class, 'cannot be declared in backend artifacts');
    });

    it('throws when Ability type ACT declared on non-action artifact', function () {
        expect(fn() => Ui5Registry::fromArray([
            'modules' => [
                'foo' => \Tests\Fixture\Hello\Errors\Ability\ActAbility\Module::class,
            ]
        ]))->toThrow(LogicException::class, 'declared on an executable artifact');
    });

    it('throws when Ability with same name per namespace', function () {
        expect(fn() => Ui5Registry::fromArray([
            'modules' => [
                'foo' => \Tests\Fixture\Hello\Errors\Ability\DoubledAbility\Module::class,
            ]
        ]))->toThrow(LogicException::class, 'Duplicate ability');
    });
});

