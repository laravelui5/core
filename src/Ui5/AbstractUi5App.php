<?php

namespace LaravelUi5\Core\Ui5;

use Flat3\Lodata\Endpoint;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppSource;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;

abstract class AbstractUi5App extends Endpoint implements Ui5AppInterface
{
    private ?Ui5AppSource $source = null;

    public function __construct(protected Ui5ModuleInterface $module)
    {
        parent::__construct($module->getSlug());

        if ($module->getSource() instanceof Ui5AppSource) {
            $this->source = $module->getSource();
        }
    }

    public function getModule(): Ui5ModuleInterface
    {
        return $this->module;
    }

    public function getSource(): ?Ui5AppSource
    {
        return $this->source;
    }

    public function getManifestPath(): string
    {
        if ($this->source) {
            return $this->source->getSourcePath() . '/manifest.json';
        }

        return __DIR__ . '/../resources/ui5/manifest.json';
    }
}
