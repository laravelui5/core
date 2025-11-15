<?php

use LaravelUi5\Core\Ui5\Ui5Registry;
use Tests\Fixture\Hello\Hello;
use Tests\Fixture\Hello\HelloManifest;

beforeEach(function () {
    $this->registry = Ui5Registry::fromArray([
        'modules' => [
            Hello::SLUG => \Tests\Fixture\Hello\HelloModule::class,
            'orders' => \Tests\Fixture\Hello\OrderModule::class,
        ]
    ]);
    $this->manifest = new HelloManifest($this->registry);
});

describe('AbstractManifest->buildIntents', function () {

    it('returns all valid actions with method and url', function () {
        $intents = invokePrivateMethod($this->manifest, 'buildIntents', Hello::SLUG);
        expect($intents)
            ->toBeArray()
            ->toHaveKey('Order')
            ->and($intents['Order'])
            ->toBeArray()
            ->toHaveKey('detail')
            ->and($intents['Order']['detail'])
            ->toMatchArray([
                'label' => 'Order Details',
                'icon'  => null,
            ]);
    });
});
