<?php

namespace LaravelUi5\Core;

use LaravelUi5\Core\Contracts\Ui5Source;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5LibraryInterface;
use LaravelUi5\Core\Ui5\AbstractUi5Module;

class ReportModule extends AbstractUi5Module
{
    public function getName(): string
    {
        return 'Report';
    }

    public function hasApp(): bool
    {
        return true;
    }

    public function getApp(): ?Ui5AppInterface
    {
        return new ReportApp($this);
    }

    public function hasLibrary(): bool
    {
        return false;
    }

    public function getLibrary(): ?Ui5LibraryInterface
    {
        return null;
    }

    public function getArtifactRoot(): Ui5ArtifactInterface
    {
        return $this->getApp();
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

    public function getDashboards(): array
    {
        return [];
    }

    public function getReports(): array
    {
        return [];
    }

    public function getDialogs(): array
    {
        return [];
    }
}
