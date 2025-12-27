<?php

use LaravelUi5\Core\Internal\Ui5SourceMap;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppSource;
use LaravelUi5\Core\Ui5\Contracts\Ui5I18n;

describe('Ui5I18n', function () {
    it('resolves metadata from ui5.yaml', function () {
        $path = __DIR__ . '/../../Fixture/.ui5-sources.php';
        $map = Ui5SourceMap::load($path);
        $source = $map->forModule('Hello');
        $i18n = $source->getI18n();
        $locales = $i18n->getAvailableLocales();
        expect($source)
            ->toBeInstanceOf(Ui5AppSource::class)
            ->and($i18n)
            ->toBeInstanceOf(Ui5I18n::class)
            ->and($i18n->getTitle())
            ->toBe('LaravelUi5/Core (Fallback Locale)')
            ->and($i18n->getDescription())
            ->toBe('Test fixtures for the LaravelUi5/Core package (Fallback Locale)')
            ->and(count($locales))
            ->toBe(2)
            ->and($locales)
            ->toContain('de')
            ->and($i18n->getTitle('de'))
            ->toBe('LaravelUi5/Core (German Locale)')
            ->and($i18n->getDescription('de'))
            ->toBe('Test fixtures for the LaravelUi5/Core package (German Locale)');
    });
});
