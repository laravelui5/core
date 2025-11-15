<?php

use Tests\Fixture\Hello\Hello;
use Tests\Fixture\Hello\HelloManifest;
use LaravelUi5\Core\Ui5\Ui5Registry;

describe('AbstractManifest->buildMeta', function () {

    it('returns the default generator when no meta config exists', function () {
        $registry = Ui5Registry::fromArray(Hello::ui5Config());
        $manifest = new HelloManifest($registry);

        // Act
        $meta = invokePrivateMethod($manifest, 'buildMeta');

        // Assert
        expect($meta)
            ->toHaveKey('generator', 'LaravelUi5 Core')
            ->and($meta)->toHaveCount(1);
    });

    it('merges configured meta values with default generator', function () {
        config()->set('ui5.meta', [
            'version' => '1.0.0',
            'author'  => 'Test Author',
        ]);

        $registry = Ui5Registry::fromArray(Hello::ui5Config());
        $manifest = new HelloManifest($registry);

        // Act
        $meta = invokePrivateMethod($manifest, 'buildMeta');

        // Assert
        expect($meta)
            ->toHaveKey('generator', 'LaravelUi5 Core')
            ->and($meta)->toHaveKey('version', '1.0.0')
            ->and($meta)->toHaveKey('author', 'Test Author');
    });

    it('does not allow generator to be overwritten by config', function () {
        // Arrange: config überschreibt absichtlich den generator
        config()->set('ui5.meta', [
            'generator' => 'Something Else',
            'build' => '2025-11-15',
        ]);

        $registry = Ui5Registry::fromArray(Hello::ui5Config());
        $manifest = new HelloManifest($registry);

        // Act
        $meta = invokePrivateMethod($manifest, 'buildMeta');

        // Assert
        // Wichtig: der generator bleibt unverändert
        expect($meta['generator'])->toBe('LaravelUi5 Core')
            ->and($meta['build'])->toBe('2025-11-15');
    });
});
