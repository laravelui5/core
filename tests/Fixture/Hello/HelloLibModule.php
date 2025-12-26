<?php

namespace Tests\Fixture\Hello;

use LaravelUi5\Core\Contracts\Ui5Source;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5LibraryInterface;
use LaravelUi5\Core\Ui5\AbstractUi5Module;

class HelloLibModule extends AbstractUi5Module
{

    public function getSource(): ?Ui5Source
    {
        return null;
    }

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
        return new HelloLibrary($this);
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

    public function getName(): string
    {
        return 'HelloLib';
    }
}
