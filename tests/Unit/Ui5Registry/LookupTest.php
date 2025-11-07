<?php

use LaravelUi5\Core\Ui5\Ui5Registry;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;
use Tests\Fixture\Hello\Hello;

beforeEach(function () {
    $this->registry = Ui5Registry::fromArray(Hello::ui5Config());
});

describe('Lookup Layer — Modules', function () {
    it('returns true for known module slug', function () {
        expect($this->registry->hasModule(Hello::SLUG))->toBeTrue();
    });

    it('returns false for unknown module slug', function () {
        expect($this->registry->hasModule('unknown'))->toBeFalse();
    });

    it('returns correct module instance for getModule()', function () {
        $module = $this->registry->getModule(Hello::SLUG);

        expect($module)
            ->toBeInstanceOf(Ui5ModuleInterface::class)
            ->and($module->getSlug())->toBe(Hello::SLUG);
    });

    it('returns null for unknown module slug', function () {
        expect($this->registry->getModule('does-not-exist'))->toBeNull();
    });

    it('returns all registered modules as array', function () {
        $modules = $this->registry->modules();

        expect($modules)
            ->toBeArray()
            ->toHaveKey(Hello::SLUG)
            ->and($modules[Hello::SLUG])->toBeInstanceOf(Ui5ModuleInterface::class);
    });
});

describe('Lookup Layer — Artifacts', function () {
    it('returns true for known artifact namespace', function () {
        $knownNamespace = Hello::NAMESPACE;
        expect($this->registry->has($knownNamespace))->toBeTrue();
    });

    it('returns false for unknown artifact namespace', function () {
        expect($this->registry->has('App\\Ui5\\DoesNotExist'))->toBeFalse();
    });

    it('returns artifact instance for known namespace', function () {
        $namespace = Hello::NAMESPACE;
        $artifact = $this->registry->get($namespace);

        expect($artifact)->toBeInstanceOf(Ui5ArtifactInterface::class)
            ->and($artifact->getNamespace())->toBe($namespace);
    });

    it('returns null for unknown artifact namespace', function () {
        expect($this->registry->get('App\\Ui5\\Foo\\Bar'))->toBeNull();
    });

    it('returns all registered artifacts as array', function () {
        $all = $this->registry->all();

        expect($all)->toBeArray()
            ->toHaveKey(Hello::NAMESPACE)
            ->and(array_values($all)[0])->toBeInstanceOf(Ui5ArtifactInterface::class);
    });
});
