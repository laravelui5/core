<?php

use Fixtures\Hello\Hello;
use LaravelUi5\Core\Ui5\Ui5Registry;

describe('Resource path resolution', function () {
    it('resolves resource path for namespace', function () {
        $registry = Ui5Registry::fromArray(Hello::ui5Config());
        $path = $registry->resolve(Hello::NAMESPACE);
        expect($path)->toStartWith('/ui5/app/com/laravelui5/hello@1.0.0');
    });

    it('resolves multiple roots correctly', function () {
        $registry = Ui5Registry::fromArray(Hello::ui5Config());
        $roots = $registry->resolveRoots([Hello::NAMESPACE]);
        expect($roots)->toHaveKey(Hello::NAMESPACE)
            ->and($roots[Hello::NAMESPACE])->toStartWith('/ui5/app/com/laravelui5/hello@1.0.0');
    });
});

describe('Introspect snapshot', function () {
    it('returns all expected keys in introspection array', function () {
        $registry = Ui5Registry::fromArray(Hello::ui5Config());
        $data = $registry->exportToCache();

        expect($data)->toHaveKeys([
            'modules', 'artifacts', 'namespaceToModule', 'artifactToModule', 'settings'
        ]);
    });
});
