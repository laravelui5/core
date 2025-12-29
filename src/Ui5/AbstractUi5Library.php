<?php

namespace LaravelUi5\Core\Ui5;

use LaravelUi5\Core\Introspection\Library\Ui5LibrarySource;
use LaravelUi5\Core\Ui5\Contracts\Ui5LibraryInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;

abstract class AbstractUi5Library implements Ui5LibraryInterface
{
    public function __construct(protected Ui5ModuleInterface $module)
    {
    }

    public function getModule(): Ui5ModuleInterface
    {
        return $this->module;
    }

    public function getSource(): Ui5LibrarySource
    {
        return $this->module->getSourceStrategy()->createLibrarySource($this->getVendor());
    }
}
