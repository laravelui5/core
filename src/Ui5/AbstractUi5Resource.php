<?php

namespace LaravelUi5\Core\Ui5;

use LaravelUi5\Core\Enums\ArtifactType;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ResourceInterface;

abstract class AbstractUi5Resource implements Ui5ResourceInterface
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
        return ArtifactType::Resource;
    }
}
