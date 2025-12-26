<?php

use LaravelUi5\Core\Ui5\Ui5Registry;
use Tests\Fixture\Hello\Hello;

describe('Slug resolution', function () {
    it('returns artifact for known slug', function () {
        $registry = Ui5Registry::fromArray(Hello::ui5Config());
        $slug = 'app/hello';

        $artifact = $registry->fromSlug($slug);
        expect($artifact)->not->toBeNull()
            ->and($artifact->getNamespace())->toContain(Hello::NAMESPACE);
    });

    it('returns null for unknown slug', function () {
        $registry = Ui5Registry::fromArray(Hello::ui5Config());
        expect($registry->fromSlug('app/unknown'))->toBeNull();
    });
});

describe('Resource path resolution', function () {
    it('resolves resource path for namespace', function () {
        $registry = Ui5Registry::fromArray(Hello::ui5Config());
        $path = $registry->resolve(Hello::NAMESPACE);
        expect($path)->toStartWith('/ui5/app/hello/');
    });

    it('resolves multiple roots correctly', function () {
        $registry = Ui5Registry::fromArray(Hello::ui5Config());
        $roots = $registry->resolveRoots([Hello::NAMESPACE]);
        expect($roots)->toHaveKey(Hello::NAMESPACE)
            ->and($roots[Hello::NAMESPACE])->toStartWith('/ui5/app/hello/');
    });
});

describe('Introspect snapshot', function () {
    it('returns all expected keys in introspection array', function () {
        $registry = Ui5Registry::fromArray(Hello::ui5Config());
        $data = $registry->exportToCache();

        expect($data)->toHaveKeys([
            'modules', 'artifacts', 'namespaceToModule', 'slugs',
            'artifactToModule', 'settings'
        ]);
    });
});
