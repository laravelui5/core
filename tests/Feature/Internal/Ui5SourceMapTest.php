<?php

use LaravelUi5\Core\Internal\Ui5SourceMap;

use LaravelUi5\Core\Ui5\Contracts\Ui5AppSource;

function getUi5SourceMap(): Ui5SourceMap
{
    $path = __DIR__ . '/../../Fixture/.ui5-sources.php';
    return Ui5SourceMap::load($path);
}

describe('Ui5SourceMap', function () {
    it('loads a source map from file', function () {
        expect(getUi5SourceMap())->toBeInstanceOf(Ui5SourceMap::class);
    });

    it('returns null for unknown module', function () {
        $map = getUi5SourceMap();
        expect($map->forModule('DoesNotExist'))->toBeNull();
    });

    it('resolves an app source from the source map', function () {
        $map = getUi5SourceMap();
        $source = $map->forModule('Hello');
        expect($source)
            ->toBeInstanceOf(Ui5AppSource::class)
            ->and($source->isDev())
            ->toBe(false)
            ->and($source->getDescriptor()->getVendor())
            ->toBe('Pragmatiqu IT GmbH');
    });
});
