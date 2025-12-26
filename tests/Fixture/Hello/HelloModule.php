<?php

namespace Tests\Fixture\Hello;

use LaravelUi5\Core\Contracts\Ui5Source;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5LibraryInterface;
use LaravelUi5\Core\Ui5\AbstractUi5Module;

class HelloModule extends AbstractUi5Module
{
    public function getSource(): ?Ui5Source
    {
        return null;
    }

    public function hasApp(): bool
    {
        return true;
    }

    public function getApp(): ?Ui5AppInterface
    {
        return new HelloApp($this);
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
        return [
            new Cards\WorkHours\Card($this)
        ];
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
        return [
            new Actions\World\Action($this),
        ];
    }

    public function getResources(): array
    {
        return [
            new Resources\First\Resource($this)
        ];
    }

    public function getName(): string
    {
        return 'Hello';
    }

    public function getDashboards(): array
    {
        return [];
    }

    public function getReports(): array
    {
        return [
            new Reports\World\Report($this),
        ];
    }

    public function getDialogs(): array
    {
        return [];
    }
}
