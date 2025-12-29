<?php

use LaravelUi5\Core\Introspection\App\Ui5AppSource;

describe('Ui5PackageMeta', function () {
    it('resolves metadata from package.json', function () {
        $source = getAppSource();
        expect($source)
            ->toBeInstanceOf(Ui5AppSource::class)
            ->and($source->getPackageMeta()->getName())
            ->toBe('@pragmatiqu/portal')
            ->and($source->getPackageMeta()->getVersion())
            ->toBe('0.3.0')
            ->and($source->getPackageMeta()->getBuilder())
            ->toBe('ui5 build --clean-dest');
    });
});
