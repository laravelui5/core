<?php

namespace LaravelUi5\Core;

use LaravelUi5\Core\Attributes\Role;
use LaravelUi5\Core\Enums\SettingVisibilityRole;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5LibraryInterface;
use LaravelUi5\Core\Ui5\AbstractUi5Module;


#[
    Role(SettingVisibilityRole::SuperAdmin->name, 'System-wide administrative control across all tenants. Used exclusively by the SaaS provider.'),
    Role(SettingVisibilityRole::TenantAdmin->name, 'Responsible for setting up and maintaining tenants on behalf of the customer (typically a consultant).'),
    Role(SettingVisibilityRole::SiteAdmin->name, 'Internal admin user at the customer site. Manages users, settings, and internal configuration.'),
    Role(SettingVisibilityRole::Supervisor->name, 'Team or departmental lead with operational oversight, planning, and reporting responsibilities.'),
    Role(SettingVisibilityRole::Employee->name, 'Default role for all internal users. Grants basic access to features relevant to regular staff.')
]
class CoreModule extends AbstractUi5Module
{
    public function hasApp(): bool
    {
        return false;
    }

    public function getApp(): ?Ui5AppInterface
    {
        return null;
    }

    public function hasLibrary(): bool
    {
        return true;
    }

    public function getLibrary(): ?Ui5LibraryInterface
    {
        return new CoreLibrary($this);
    }

    public function getArtifactRoot(): Ui5ArtifactInterface
    {
        return $this->getLibrary();
    }

    public function getCards(): array
    {
        return [];
    }

    public function getKpis(): array
    {
        return [];
    }

    public function getTiles(): array
    {
        return [];
    }

    public function getActions(): array
    {
        return [];
    }

    public function getResources(): array
    {
        return [];
    }
}
