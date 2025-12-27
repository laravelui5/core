<?php

use LaravelUi5\Core\Internal\Ui5SourceMap;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppDescriptor;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppSource;
use LaravelUi5\Core\Ui5\Contracts\Ui5Route;
use LaravelUi5\Core\Ui5\Contracts\Ui5Target;

describe('Ui5AppDescriptor', function () {
    it('resolves metadata from ui5.yaml', function () {
        $path = __DIR__ . '/../../Fixture/.ui5-sources.php';
        $map = Ui5SourceMap::load($path);
        $source = $map->forModule('Hello');
        $descriptor = $source->getDescriptor();
        $dependencies = $descriptor->getDependencies();
        $routes = $descriptor->getRoutes();
        $targets = $descriptor->getTargets();
        expect($source)
            ->toBeInstanceOf(Ui5AppSource::class)
            ->and($descriptor)
            ->toBeInstanceOf(Ui5AppDescriptor::class)
            ->and($descriptor->getNamespace())
            ->toBe('io.pragmatiqu.portal')
            ->and($descriptor->getTitle())
            ->toBe('LaravelUi5/Core (Fallback Locale)')
            ->and($descriptor->getDescription())
            ->toBe('Test fixtures for the LaravelUi5/Core package (Fallback Locale)')
            ->and($descriptor->getVendor())
            ->toBe('Pragmatiqu IT GmbH')
            ->and($descriptor->getVersion())
            ->toBe('APP_VERSION')
            ->and(count($dependencies))
            ->toBe(2)
            ->and($dependencies[0])
            ->toBe('sap.ui.core')
            ->and($dependencies[1])
            ->toBe('sap.m')
            ->and(count($routes))
            ->toBe(6)
            ->and($routes[0])
            ->toBeInstanceOf(Ui5Route::class)
            ->and($routes[0]->name)
            ->toBe('Portal')
            ->and($routes[0]->pattern)
            ->toBe('')
            ->and($routes[0]->target)
            ->toBe('Portal')
            ->and(count($targets))
            ->toBe(7)
            ->and($targets[0])
            ->toBeInstanceOf(Ui5Target::class)
            ->and($targets[0]->key)
            ->toBe('Portal')
            ->and($targets[0]->name)
            ->toBe('Portal');
    });
});
