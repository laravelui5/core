<?php

namespace LaravelUi5\Core;

use LaravelUi5\Core\Ui5\AbstractUi5Module;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5Infrastructure;

class ReportModule extends AbstractUi5Module implements Ui5Infrastructure
{
    public function getName(): string
    {
        return 'com.laravelui5.report';
    }

    public function getApp(): ?Ui5AppInterface
    {
        return new ReportApp($this);
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
