<?php

use Fixtures\Hello\Hello;
use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;
use LaravelUi5\Core\Ui5\Ui5Registry;

beforeEach(function () {
    $this->registry = Ui5Registry::fromArray(Hello::ui5Config());
});

describe('Lookup Layer — Modules', function () {

    it('returns correct module instance for getModule()', function () {
        $module = $this->registry->getModule(Hello::NAMESPACE);

        expect($module)
            ->toBeInstanceOf(Ui5ModuleInterface::class)
            ->and($module->getName())->toBe(Hello::NAMESPACE);
    });

    it('returns null for unknown module slug', function () {
        expect($this->registry->getModule('does-not-exist'))->toBeNull();
    });

    it('returns all registered modules as array', function () {
        $modules = $this->registry->modules();

        expect($modules)->toBeArray()
            ->and($modules)
            ->toHaveCount(1)
            ->and($modules['com.laravelui5.hello'])
            ->toBeInstanceOf(Ui5ModuleInterface::class)
            ->and($modules['com.laravelui5.hello']->getName())
            ->toBe(Hello::NAMESPACE);
    });
});

describe('Lookup Layer — Artifacts', function () {
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
        $all = $this->registry->artifacts();

        expect($all)->toBeArray()
            ->toHaveKey(Hello::NAMESPACE)
            ->and(array_values($all)[0])->toBeInstanceOf(Ui5ArtifactInterface::class);
    });
});
