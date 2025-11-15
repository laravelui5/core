<?php

use LaravelUi5\Core\Enums\SettingVisibilityRole;
use LaravelUi5\Core\Ui5\Ui5Registry;
use Tests\Fixture\Hello\HelloManifest;

beforeEach(function () {
    $this->registry = Ui5Registry::fromArray([
        'modules' => [
            'core' => \LaravelUi5\Core\CoreModule::class,
        ],
    ]);

    $this->manifest = new HelloManifest($this->registry);
});

describe('AbstractManifest::buildRoles', function () {

    it('extracts all Role attributes from CoreModule', function () {
        $roles = invokePrivateMethod($this->manifest, 'buildRoles');

        // Assert
        expect($roles)
            ->toBeArray()
            ->toHaveCount(5)
            ->toHaveKeys([
                SettingVisibilityRole::SuperAdmin->name,
                SettingVisibilityRole::TenantAdmin->name,
                SettingVisibilityRole::SiteAdmin->name,
                SettingVisibilityRole::Supervisor->name,
                SettingVisibilityRole::Employee->name,
            ]);

        foreach ($roles as $role => $note) {
            expect($note)
                ->toBeString()
                ->and(strlen($note))->toBeGreaterThan(10);
        }
    });

    it('returns empty array when no roles registered', function () {
        $emptyRegistry = Ui5Registry::fromArray(['modules' => []]);
        $manifest = new HelloManifest($emptyRegistry);

        $roles = invokePrivateMethod($manifest, 'buildRoles');

        expect($roles)->toBeEmpty();
    });
});
