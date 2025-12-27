<?php

namespace LaravelUi5\Core\Ui5;

use LaravelUi5\Core\Ui5\Contracts\Ui5LibraryInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5LibrarySource;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;

abstract class AbstractUi5Library implements Ui5LibraryInterface
{
    private ?Ui5LibrarySource $source = null;

    public function __construct(protected Ui5ModuleInterface $module)
    {
        if ($module->getSource() instanceof Ui5LibrarySource) {
            $this->source = $module->getSource();
        }
    }

    public function getModule(): Ui5ModuleInterface
    {
        return $this->module;
    }

    public function getSource(): ?Ui5LibrarySource
    {
        return $this->source;
    }
}
