<?php

use Fixtures\Hello\Hello;
use Fixtures\Hello\HelloManifest;
use LaravelUi5\Core\Ui5\Ui5Registry;

beforeEach(function () {
    $this->registry = Ui5Registry::fromArray(Hello::ui5Config());
    $this->manifest = new HelloManifest($this->registry);
    $this->module = $this->registry->getModule(Hello::NAMESPACE);
});

describe('AbstractManifest->buildActions', function () {

    it('returns all valid actions with method and url', function () {
        $actions = invokePrivateMethod($this->manifest, 'buildActions', $this->module);

        expect($actions)
            ->toBeArray()
            ->toHaveKey(Hello::ACTION_SLUG);

        $action = $actions[Hello::ACTION_SLUG];
        expect($action)
            ->toHaveKeys(['method', 'url'])
            ->and($action['method'])
            ->toBeString()
            ->and($action['url'])
            ->toStartWith('/ui5/')
            ->and($action['url'])
            ->toContain(str_replace('.', '/',Hello::ACTION_SLUG));
    });

    it('appends path parameters to the url when defined', function () {

    })->skip('Path parameter test not implemented yet');

    it('returns empty array when module has no actions', function () {
        $registry = Ui5Registry::fromArray([
            'modules' => [
                'hello' => \Fixtures\Hello\HelloLibModule::class,
            ]
        ]);
        $manifest = new HelloManifest($registry);
        $module = $registry->getModule('com.laravelui5.hello.lib');

        $actions = invokePrivateMethod($manifest, 'buildActions', $module);

        expect($actions)->toBe([]);
    });
});
