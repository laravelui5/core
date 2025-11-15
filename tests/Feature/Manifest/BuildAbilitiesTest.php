<?php

use LaravelUi5\Core\Ui5\Ui5Registry;
use Tests\Fixture\Hello\Hello;
use Tests\Fixture\Hello\HelloManifest;

beforeEach(function () {
    $this->registry = Ui5Registry::fromArray(Hello::ui5Config());
    $this->manifest = new HelloManifest($this->registry);
    $this->namespace = Hello::NAMESPACE;
});

describe('AbstractManifest->buildAbilities', function () {

    it('returns flattened boolean map of abilities without access key', function () {
        // Act
        $abilities = invokePrivateMethod($this->manifest, 'buildAbilities', Hello::NAMESPACE);

        // Assert
        expect($abilities)
            ->toBeArray()
            ->toHaveKey('act')
            ->and($abilities['act'])->toHaveKey('toggleLock')
            ->and($abilities['act']['toggleLock'])->toBeBool()
            ->and($abilities)->not->toHaveKey('access');
    });

    it('sets ability flags to true in DEV environment', function () {
        config()->set('ui5.active', 'DEV');

        $abilities = invokePrivateMethod($this->manifest, 'buildAbilities', Hello::NAMESPACE);

        expect($abilities['act']['toggleLock'])->toBeTrue();
    });

    it('sets ability flags to false in PRO environment w/out defining active', function () {
        $abilities = invokePrivateMethod($this->manifest, 'buildAbilities', Hello::NAMESPACE);

        expect($abilities['act']['toggleLock'])->toBeTrue();
    });
});
