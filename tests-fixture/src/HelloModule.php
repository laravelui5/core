<?php

namespace Fixtures\Hello;

use LaravelUi5\Core\Ui5\AbstractUi5Module;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5LibraryInterface;

class HelloModule extends AbstractUi5Module
{
    public function getName(): string
    {
        return 'com.laravelui5.hello';
    }

    public function getApp(): ?Ui5AppInterface
    {
        return new HelloApp($this);
    }

    public function getCards(): array
    {
        return [
            new \Fixtures\Hello\Cards\WorkHours\Card($this)
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
            new \Fixtures\Hello\Actions\World\Action($this),
        ];
    }

    public function getResources(): array
    {
        return [
            new \Fixtures\Hello\Resources\First\Resource($this)
        ];
    }

    public function getDashboards(): array
    {
        return [
            new \Fixtures\Hello\Dashboards\World($this)
        ];
    }

    public function getReports(): array
    {
        return [
            new \Fixtures\Hello\Reports\World\Report($this),
        ];
    }

    public function getDialogs(): array
    {
        return [];
    }
}
