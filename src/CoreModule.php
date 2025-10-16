<?php

namespace LaravelUi5\Core;

use LaravelUi5\Core\Attributes\Role;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5LibraryInterface;
use LaravelUi5\Core\Ui5\Ui5Module;


#[
    Role('Super Administrator', 'System-wide administrative control across all tenants. Used exclusively by the SaaS provider.'),
    Role('Tenant Administrator', 'Responsible for setting up and maintaining tenants on behalf of the customer (typically a consultant).'),
    Role('Site Administrator', 'Internal admin user at the customer site. Manages users, settings, and internal configuration.'),
    Role('Supervisor', 'Team or departmental lead with operational oversight, planning, and reporting responsibilities.'),
    Role('Employee', 'Default role for all internal users. Grants basic access to features relevant to regular staff.')
]
class CoreModule extends Ui5Module
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
