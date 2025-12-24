<?php

namespace Tests\Fixture\Hello\Resources\First;

use LaravelUi5\Core\Enums\ArtifactType;
use LaravelUi5\Core\Ui5\Contracts\DataProviderInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ResourceInterface;

class Resource implements Ui5ResourceInterface
{

    public function __construct(protected Ui5ModuleInterface $module)
    {
    }

    public function getModule(): Ui5ModuleInterface
    {
        return $this->module;
    }

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

    public function getSlug(): string
    {
        return 'first';
    }

    public function getProvider(): DataProviderInterface
    {
        return new Provider();
    }
}
