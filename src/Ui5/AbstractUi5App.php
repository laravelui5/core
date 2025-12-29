<?php

namespace LaravelUi5\Core\Ui5;

use Flat3\Lodata\Endpoint;
use LaravelUi5\Core\Introspection\App\Ui5AppSource;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;

abstract class AbstractUi5App extends Endpoint implements Ui5AppInterface
{
    public function __construct(protected Ui5ModuleInterface $module)
    {
        parent::__construct($module->getSlug());
    }

    public function getModule(): Ui5ModuleInterface
    {
        return $this->module;
    }

    public function getSource(): Ui5AppSource
    {
        return $this->module->getSourceStrategy()->createAppSource($this->getVendor());
    }

    public function getManifestPath(): string
    {
        return $this->module->getSourceStrategy()->getSourcePath() . '/manifest.json';
    }
}
