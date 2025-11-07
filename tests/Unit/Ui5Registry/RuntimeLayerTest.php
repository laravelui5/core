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

    it('computes slugFor correctly', function () {
        $registry = Ui5Registry::fromArray(Hello::ui5Config());
        $artifact = $registry->get(Hello::NAMESPACE);
        expect($registry->slugFor($artifact))->toContain('app/hello');
    });
});

describe('Namespace and artifact mapping', function () {
    it('maps namespace to correct module slug', function () {
        $registry = Ui5Registry::fromArray(Hello::ui5Config());
        expect($registry->namespaceToModuleSlug(Hello::NAMESPACE))->toBe('hello');
    });

    it('maps artifact class to correct module slug', function () {
        $registry = Ui5Registry::fromArray(Hello::ui5Config());
        $artifact = $registry->get(Hello::NAMESPACE);
        expect($registry->artifactToModuleSlug($artifact::class))->toBe('hello');
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

describe('Semantic intents resolution', function () {
    it('returns incoming intents for a module', function () {
        $config = [
            'modules' => [
                'user' => \Tests\Fixture\Hello\HelloModule::class,
                'order' => \Tests\Fixture\Hello\Order2Module::class,
            ]
        ];
        $registry = Ui5Registry::fromArray($config);
        $intents = $registry->resolveIntents('user');

        expect($intents)->toHaveKey('Order')
            ->and($intents['Order'])->toHaveKey('detail')
            ->and($intents['Order']['detail']['label'])->toBe('Order Details');
    });

    it('returns empty array for module without incoming links', function () {
        $config = [
            'modules' => [
                'user' => \Tests\Fixture\Hello\HelloModule::class,
                'order' => \Tests\Fixture\Hello\Order2Module::class,
            ]
        ];
        $registry = Ui5Registry::fromArray($config);
        expect($registry->resolveIntents('order'))->toBeEmpty();
    });
});

describe('Introspect snapshot', function () {
    it('returns all expected keys in introspection array', function () {
        $registry = Ui5Registry::fromArray(Hello::ui5Config());
        $data = $registry->introspect();

        expect($data)->toHaveKeys([
            'modules', 'artifacts', 'namespaceToModule', 'slugs',
            'roles', 'abilities', 'objects', 'links'
        ]);
    });
});
