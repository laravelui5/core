<?php

use LaravelUi5\Core\Ui5\Ui5Registry;
use Tests\Fixture\Hello\Hello;
use Tests\Fixture\Hello\HelloManifest;

beforeEach(function () {
    $this->registry = Ui5Registry::fromArray(Hello::ui5Config());
    $this->manifest = new HelloManifest($this->registry);
});

describe('AbstractManifest->buildSettings', function () {

    it('returns a simple key-value map of default setting values', function () {
        $settings = invokePrivateMethod($this->manifest, 'buildSettings', Hello::NAMESPACE);

        expect($settings)
            ->toBeArray()
            ->toHaveKeys(['darkMode', 'maxItems'])
            ->and($settings['darkMode'])->toBeBool()->toBeFalse()
            ->and($settings['maxItems'])->toBeInt();
    });

    it('returns empty array when no settings are discovered', function () {
        $registry = Ui5Registry::fromArray([
            'modules' => [
                'hello' => \Tests\Fixture\Hello\HelloLibModule::class,
            ]
        ]);
        $manifest = new HelloManifest($registry);
        $settings = invokePrivateMethod($manifest, 'buildSettings', 'com.laravelui5.hello');

        expect($settings)->toBe([]);
    });
});
