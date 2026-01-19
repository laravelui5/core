<?php

namespace Fixtures\Hello\Dashboards;

use LaravelUi5\Core\Enums\ArtifactType;
use LaravelUi5\Core\Ui5\AbstractUi5Dashboard;

class World extends AbstractUi5Dashboard
{
    public function getType(): ArtifactType
    {
        return ArtifactType::Dashboard;
    }

    public function getNamespace(): string
    {
        return 'com.laravelui5.hello.dashboards.world';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }

    public function getTitle(): string
    {
        return 'World';
    }

    public function getDescription(): string
    {
        return 'Dashboard for World';
    }
}
