<?php

namespace LaravelUi5\Core\Ui5;

use LaravelUi5\Core\Enums\ArtifactType;
use LaravelUi5\Core\Ui5\Contracts\Ui5ActionInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;

abstract class AbstractUi5Action implements Ui5ActionInterface
{
    public function __construct(protected Ui5ModuleInterface $module)
    {
    }

    public function getType(): ArtifactType
    {
        return ArtifactType::Action;
    }

    public function getModule(): Ui5ModuleInterface
    {
        return $this->module;
    }
}
