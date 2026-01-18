<?php

use Fixtures\Hello\Hello;
use Fixtures\Hello\HelloManifest;
use LaravelUi5\Core\Ui5\Ui5Registry;

beforeEach(function () {
    $this->registry = Ui5Registry::fromArray(Hello::ui5Config());
    $this->manifest = new HelloManifest($this->registry);
    $this->module = $this->registry->getModule(Hello::NAMESPACE);
});

describe('AbstractManifest->buildResources', function () {

    it('returns all valid resources with method GET and correct url', function () {
        // Act
        $resources = invokePrivateMethod($this->manifest, 'buildResources', $this->module);

        // Assert
        expect($resources)
            ->toBeArray()
            ->toHaveKey('first');

        $userData = $resources['first'];

        expect($userData)
            ->toMatchArray([
                'method' => 'GET',
                'url' => '/ui5/resource/com/laravelui5/hello/resources/first@1.0.0/',
            ]);
    });

    it('appends path parameters if provider defines Parameter attributes', function () {

    })->skip('Not yet implemented — will verify {id} placeholders later.');

    it('returns empty array when module has no resources', function () {
        $registry = Ui5Registry::fromArray([
            'modules' => [
                'hello' => \Fixtures\Hello\HelloLibModule::class,
            ]
        ]);
        $manifest = new HelloManifest($registry);
        $module = $registry->getModule('com.laravelui5.hello.lib');

        $resources = invokePrivateMethod($manifest, 'buildResources', $module);

        expect($resources)->toBe([]);
    });
});
