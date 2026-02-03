<?php

use Fixtures\Hello\Settings\DuplicateSettingsHandler;
use Fixtures\Hello\Settings\ExistingPropertyHandler;
use Fixtures\Hello\Settings\NotExtendingAbstractConfigurableHandler;
use Fixtures\Hello\Settings\SettingHandler;
use LaravelUi5\Core\Exceptions\InvalidSettingException;
use LaravelUi5\Core\Services\SettingResolver;
use LaravelUi5\Core\Ui5\AbstractConfigurable;

describe('SettingResolver', function () {

    it('injects default settings into configurable handler', function () {
        $handler = new SettingHandler();
        $resolver = new SettingResolver();

        $resolver->resolve($handler);

        expect($handler->maxItems)
            ->toBe(10)
            ->and($handler->enabled)
            ->toBeTrue();
    });

    it('throws when accessing an undefined setting', function () {
        $handler = new SettingHandler();
        $resolver = new SettingResolver();

        $resolver->resolve($handler);

        $handler->doesNotExist; // 💥
    })->throws(LogicException::class);

    it('throws when resolving settings on invalid target', function () {
        $handler = new NotExtendingAbstractConfigurableHandler();
        $resolver = new SettingResolver();

        $resolver->resolve($handler);
    })->throws(InvalidSettingException::class);

    it('allows configurable target without settings', function () {
        $handler = new class extends AbstractConfigurable {
            public function handle(): void
            {
            }
        };

        $resolver = new SettingResolver();

        $resolver->resolve($handler);

        expect(true)->toBeTrue();
    });

    it('throws on duplicate setting keys', function () {
        $resolver = new SettingResolver();
        $handler = new DuplicateSettingsHandler();

        $resolver->resolve($handler);
    })->throws(InvalidSettingException::class);

    it('throws on existing setting property', function () {
        $resolver = new SettingResolver();
        $handler = new ExistingPropertyHandler();

        $resolver->resolve($handler);
    })->throws(InvalidSettingException::class);

});
