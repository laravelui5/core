<?php

use LaravelUi5\Core\Introspection\App\Ui5AppSource;

describe('Ui5Framework', function () {
    it('resolves metadata from ui5.yaml', function () {
        $source = getAppSource();
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
