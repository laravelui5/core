<?php

use LaravelUi5\Core\Ui5\Ui5Registry;
use Tests\Fixture\Hello\Hello;
use Tests\Fixture\Hello\Models\User;

describe('SemanticObject discovery', function () {
    it('registers a valid SemanticObject correctly', function () {
        $registry = Ui5Registry::fromArray(Hello::ui5Config());
        $objects = $registry->objects();

        expect($objects)
            ->toBeArray()
            ->toHaveKey(User::class)
            ->and($objects[User::class])
            ->toHaveKey('routes')
            ->and($objects[User::class]['routes'])->toHaveKey('detail');
    });

    it('throws when SemanticObject model is empty', function () {
        expect(fn() => Ui5Registry::fromArray([
            'modules' => [
                'foo' => \Tests\Fixture\Hello\Errors\SemanticObject\EmptyModule::class,
            ]
        ]))->toThrow(LogicException::class, 'Invalid SemanticObject definition');
    });

    it('throws when SemanticObject route intents are empty', function () {
        expect(fn() => Ui5Registry::fromArray([
            'modules' => [
                'foo' => \Tests\Fixture\Hello\Errors\SemanticObject\MissingRoutesModule::class,
            ]
        ]))->toThrow(LogicException::class, 'define at least one route intent');
    });

    it('throws when SemanticObject model is referenced more than once', function () {
        $config = Hello::ui5Config();
        $config['modules']['foo'] = \Tests\Fixture\Hello\Errors\SemanticObject\DuplicateModelModule::class;
        expect(fn() => Ui5Registry::fromArray($config))
            ->toThrow(LogicException::class, 'already registered as a SemanticObject by');
    });
});
