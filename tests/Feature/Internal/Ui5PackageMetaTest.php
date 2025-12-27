<?php

use LaravelUi5\Core\Internal\Ui5SourceMap;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppSource;

describe('Ui5PackageMeta', function () {
    it('resolves metadata from package.json', function () {
        $path = __DIR__ . '/../../Fixture/.ui5-sources.php';
        $map = Ui5SourceMap::load($path);
        $source = $map->forModule('Hello');
        expect($source)
            ->toBeInstanceOf(Ui5AppSource::class)
            ->and($source->getPackageMeta()->name)
            ->toBe('@pragmatiqu/portal')
            ->and($source->getPackageMeta()->version)
            ->toBe('0.3.0')
            ->and($source->getPackageMeta()->builder)
            ->toBe('ui5 build --clean-dest');
    });
});
