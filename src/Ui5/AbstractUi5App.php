<?php

namespace LaravelUi5\Core\Ui5;

use Flat3\Lodata\Endpoint;
use JsonException;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppSource;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;

abstract class AbstractUi5App extends Endpoint implements Ui5AppInterface
{
    private ?Ui5AppSource $source = null;

    public function __construct(protected Ui5ModuleInterface $module)
    {
        parent::__construct($module->getSlug());
    }

    public function getModule(): Ui5ModuleInterface
    {
        return $this->module;
    }

    /**
     * @throws JsonException
     */
    public function getSource(): Ui5AppSource
    {
        if (null === $this->source) {
            $this->source = Ui5AppSource::fromFilesystem(
                $this->module->getSourcePath(),
                $this->getVendor(),
                !app()->runningInConsole()
            );
        }

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
