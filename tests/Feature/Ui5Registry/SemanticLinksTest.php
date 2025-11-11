<?php

use LaravelUi5\Core\Ui5\Ui5Registry;
use Tests\Fixture\Hello\Models\Order;
use Tests\Fixture\Hello\Models\Order2;
use Tests\Fixture\Hello\Models\User;

// Developer Note:
// Tests involving semantic objects or any logic that performs
// model reflection (e.g. inspecting relations, attributes, casts)
// must run inside the full Laravel application context (Orchestra Testbench).
//
// Those belong under `tests/Feature/` and should extend `Tests\TestCase`,
// because Eloquent, the container, and the database manager need to be booted.
//
// Otherwise, reflection on Eloquent models will silently fail
// since relation methods and Facades depend on the Laravel container.
describe('SemanticLink discovery', function () {
    it('infers SemanticLink target from Eloquent relation when model is omitted', function () {
        $config = [
            'modules' => [
                'user' => \Tests\Fixture\Hello\HelloModule::class,
                'order' => \Tests\Fixture\Hello\OrderModule::class,
            ]
        ];
        $registry = Ui5Registry::fromArray($config);

        $linksProp = new ReflectionProperty($registry, 'links');
        $links = $linksProp->getValue($registry);

        expect($links)
            ->toHaveKey(Order::class)
            ->and($links[Order::class])
            ->toContain(User::class);
    });

    it('infers SemanticLink target from attribute declaration', function () {
        $config = [
            'modules' => [
                'user' => \Tests\Fixture\Hello\HelloModule::class,
                'order' => \Tests\Fixture\Hello\Order2Module::class,
            ]
        ];
        $registry = Ui5Registry::fromArray($config);

        $linksProp = new ReflectionProperty($registry, 'links');
        $links = $linksProp->getValue($registry);

        expect($links)
            ->toHaveKey(Order2::class)
            ->and($links[Order2::class])
            ->toContain(User::class);
    });

    it('throws when SemanticLink ref model is not registered', function () {
        expect(fn() => Ui5Registry::fromArray([
            'modules' => [
                'foo' => \Tests\Fixture\Hello\Errors\SemanticLink\InvalidModule::class,
            ]
        ]))->toThrow(LogicException::class, 'points to unknown model');
    });
});
