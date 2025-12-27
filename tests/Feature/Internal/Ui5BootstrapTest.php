<?php

use LaravelUi5\Core\Internal\Ui5SourceMap;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppSource;
use LaravelUi5\Core\Ui5\Contracts\Ui5Bootstrap;

describe('Ui5Bootstrap', function () {
    it('resolves metadata from ui5.yaml', function () {
        $path = __DIR__ . '/../../Fixture/.ui5-sources.php';
        $map = Ui5SourceMap::load($path);
        $source = $map->forModule('Hello');
        $bootstrap = $source->getBootstrap();
        $attributes = $bootstrap->getAttributes();
        $namespaces = $bootstrap->getResourceNamespaces();
        expect($source)
            ->toBeInstanceOf(Ui5AppSource::class)
            ->and($bootstrap)
            ->toBeInstanceOf(Ui5Bootstrap::class)
            ->and($bootstrap->getInlineScript())
            ->toContain('sap.ui.loader.config')
            ->and($bootstrap->getInlineCss())
            ->toContain('EditorJS Tooltip ausblenden')
            ->and(count($attributes))
            ->toBe(7)
            ->and($attributes)
            ->toHaveKey('theme')
            ->and($attributes['theme'])
            ->toBe('sap_horizon')
            ->and(count($namespaces))
            ->toBe(1)
            ->and($namespaces[0])
            ->toBe('io.pragmatiqu.portal');
    });
});
