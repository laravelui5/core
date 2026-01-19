<?php

namespace Fixtures\Hello\Resources\First;

use LaravelUi5\Core\Enums\ArtifactType;
use LaravelUi5\Core\Ui5\AbstractUi5Resource;
use LaravelUi5\Core\Ui5\Capabilities\DataProviderInterface;

class Resource extends AbstractUi5Resource
{
    public function getNamespace(): string
    {
        return 'com.laravelui5.hello.resources.first';
    }

    public function getType(): ArtifactType
    {
        return ArtifactType::Resource;
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }

    public function getTitle(): string
    {
        return 'First';
    }

    public function getDescription(): string
    {
        return 'Resource for First';
    }

    public function getProvider(): DataProviderInterface
    {
        return new Provider();
    }
}
