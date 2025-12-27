<?php

use LaravelUi5\Core\Internal\Ui5SourceMap;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppSource;

describe('Ui5Framework', function () {
    it('resolves metadata from ui5.yaml', function () {
        $path = __DIR__ . '/../../Fixture/.ui5-sources.php';
        $map = Ui5SourceMap::load($path);
        $source = $map->forModule('Hello');
        expect($source)
            ->toBeInstanceOf(Ui5AppSource::class)
            ->and($source->getFramework()->name)
            ->toBe('OpenUI5')
            ->and($source->getFramework()->version)
            ->toBe('1.136.7')
            ->and($source->getFramework()->namespace)
            ->toBe('io.pragmatiqu.portal');
    });
});
