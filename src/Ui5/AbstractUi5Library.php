<?php

namespace LaravelUi5\Core\Ui5;

use JsonException;
use LaravelUi5\Core\Ui5\Contracts\Ui5LibraryInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5LibrarySource;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;

abstract class AbstractUi5Library implements Ui5LibraryInterface
{
    private ?Ui5LibrarySource $source = null;

    public function __construct(protected Ui5ModuleInterface $module)
    {
    }

    public function getModule(): Ui5ModuleInterface
    {
        return $this->module;
    }

    /**
     * @throws JsonException
     */
    public function getSource(): Ui5LibrarySource
    {
        if (null === $this->source) {
            $this->source = Ui5LibrarySource::fromFilesystem($this->module->getSourcePath());
        }

        return $this->source;
    }
}
