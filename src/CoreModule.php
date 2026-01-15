<?php

namespace LaravelUi5\Core;

use LaravelUi5\Core\Ui5\Contracts\Ui5LibraryInterface;
use LaravelUi5\Core\Ui5\AbstractUi5Module;


class CoreModule extends AbstractUi5Module
{
    public function getLibrary(): ?Ui5LibraryInterface
    {
        return new CoreLibrary($this);
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
