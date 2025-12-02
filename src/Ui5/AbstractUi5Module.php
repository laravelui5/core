<?php

namespace LaravelUi5\Core\Ui5;

use LaravelUi5\Core\Enums\ArtifactType;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;

/**
 * Abstract base class for a UI5 module definition.
 *
 * A module represents a self-contained unit of business logic and artifacts,
 * and is registered via config/ui5.php under the 'modules' key.
 * Each module is assigned a unique route-level slug used in all URL constructions.
 *
 * It may provide one App or one Library (not both), and related sub-artifacts
 * such as Cards, Reports, Tiles, and Actions. All artifact instances are expected
 * to be fully constructed and ready to register at boot time.
 */
abstract class AbstractUi5Module implements Ui5ModuleInterface
{
    /**
     * @var string
     */
    protected string $slug;

    /**
     * Create a new UI5 module instance.
     *
     * @param string $slug Route-level slug as configured in config/ui5.php
     */
    public function __construct(string $slug)
    {
        $this->slug = $slug;
    }

    public function getType(): ArtifactType
    {
        return ArtifactType::Module;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getAllArtifacts(): array
    {
        return [
            $this->getArtifactRoot(),
            ...$this->getCards(),
            ...$this->getKpis(),
            ...$this->getTiles(),
            ...$this->getActions(),
            ...$this->getResources(),
            ...$this->getReports(),
            ...$this->getDashboards(),
            ...$this->getDialogs(),
        ];
    }
}
