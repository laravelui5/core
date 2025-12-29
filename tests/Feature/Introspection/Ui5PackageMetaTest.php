<?php

use LaravelUi5\Core\Introspection\App\Ui5AppSource;

describe('Ui5PackageMeta', function () {
    it('resolves metadata from package.json', function () {
        $source = getAppSource();
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
