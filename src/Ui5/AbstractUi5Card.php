<?php

namespace LaravelUi5\Core\Ui5;

use LaravelUi5\Core\Exceptions\MissingCardManifestException;
use LaravelUi5\Core\Ui5\Contracts\Ui5CardInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;

abstract class AbstractUi5Card implements Ui5CardInterface
{
    public function __construct(protected Ui5ModuleInterface $module)
    {
    }

    public function getModule(): Ui5ModuleInterface
    {
        return $this->module;
    }

    public function getManifest(): string
    {
        $path = $this->getModule()
            ->getSourceStrategy()
            ->resolvePath("cards/{$this->getSlug()}.blade.php");

        if (!file_exists($path)) {
            throw new MissingCardManifestException($path);
        }

        return file_get_contents($path);
    }

    private function getSlug(): string
    {
        $parts = explode('.', $this->getNamespace());
        return array_pop($parts);
    }
}
