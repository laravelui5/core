<?php

namespace Fixtures\Hello\Cards\WorkHours;

use LaravelUi5\Core\Enums\ArtifactType;
use LaravelUi5\Core\Ui5\Capabilities\DataProviderInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5CardInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;

class Card implements Ui5CardInterface
{

    public function __construct(protected Ui5ModuleInterface $module)
    {
    }

    public function getModule(): Ui5ModuleInterface
    {
        return $this->module;
    }

    public function getType(): ArtifactType
    {
        return ArtifactType::Card;
    }

    public function getProvider(): DataProviderInterface
    {
        return new Provider();
    }

    public function getNamespace(): string
    {
        return 'com.laravelui5.hello.work-hours';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }

    public function getSlug(): string
    {
        return 'work-hours';
    }

    public function getTitle(): string
    {
        return 'Work Hours';
    }

    public function getDescription(): string
    {
        return 'Displays key data for Work Hours.';
    }
}
