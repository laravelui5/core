<?php

use Tests\Fixture\Hello\Hello;
use Tests\Fixture\Hello\HelloManifest;
use LaravelUi5\Core\Ui5\Ui5Registry;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::get('/hello', fn() => 'ok')->name('hello');
    Route::get('/dashboard', fn() => 'ok')->name('dashboard');
});

describe('AbstractManifest->buildRoutes', function () {

    it('returns associative array of route URLs keyed by config keys', function () {
        // Arrange
        config()->set('ui5.routes', [
            'home' => 'hello',
            'dash' => 'dashboard',
        ]);

        $registry = Ui5Registry::fromArray(Hello::ui5Config());
        $manifest = new HelloManifest($registry);

        // Act
        $routes = invokePrivateMethod($manifest, 'buildRoutes');

        // Assert
        expect($routes)
            ->toBeArray()
            ->toHaveKeys(['home', 'dash'])
            ->and($routes['home'])->toBe(url('/hello'))
            ->and($routes['dash'])->toBe(url('/dashboard'));
    });

    it('returns empty array if config is empty', function () {
        config()->set('ui5.routes', []);

        $registry = Ui5Registry::fromArray(Hello::ui5Config());
        $manifest = new HelloManifest($registry);

        $routes = invokePrivateMethod($manifest, 'buildRoutes');

        expect($routes)->toBe([]);
    });
});
