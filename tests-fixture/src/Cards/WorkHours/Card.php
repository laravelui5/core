<?php

namespace Fixtures\Hello\Cards\WorkHours;

use LaravelUi5\Core\Enums\ArtifactType;
use LaravelUi5\Core\Ui5\AbstractUi5Card;
use LaravelUi5\Core\Ui5\Capabilities\DataProviderInterface;

class Card extends AbstractUi5Card
{
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
        return 'com.laravelui5.hello.cards.work-hours';
    }

    public function getVersion(): string
    {
        return '1.0.0';
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
