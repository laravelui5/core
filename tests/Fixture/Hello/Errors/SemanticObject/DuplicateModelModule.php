<?php

namespace Tests\Fixture\Hello\Errors\SemanticObject;

use LaravelUi5\Core\Attributes\SemanticObject;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5LibraryInterface;
use LaravelUi5\Core\Ui5\AbstractUi5Module;
use Tests\Fixture\Hello\Actions;
use Tests\Fixture\Hello\Cards;
use Tests\Fixture\Hello\HelloApp;
use Tests\Fixture\Hello\Models\User;
use Tests\Fixture\Hello\Resources;

#[SemanticObject(User::class, 'User', ['detail' => ['uri' => '/detail/{id}', 'label' => 'User Details']])]
class DuplicateModelModule extends AbstractUi5Module
{
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
        return 'DuplicateModel';
    }
}
