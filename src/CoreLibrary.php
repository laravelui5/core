<?php

namespace LaravelUi5\Core;

use LaravelUi5\Core\Ui5\Contracts\Ui5LibraryInterface;
use LaravelUi5\Core\Enums\ArtifactType;
use LaravelUi5\Core\Traits\HasAssetsTrait;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;

class CoreLibrary implements Ui5LibraryInterface
{
    use HasAssetsTrait;

    public function __construct(protected Ui5ModuleInterface $module)
    {
    }

    public function getModule(): Ui5ModuleInterface
    {
        return $this->module;
    }

    public function getSlug(): string
    {
        return $this->module->getSlug();
    }

    public function getType(): ArtifactType
    {
        return ArtifactType::Library;
    }

    public function getNamespace(): string
    {
        return 'com.laravelui5.core';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }

    public function getTitle(): string
    {
        return 'Laravel Ui5 Core Library';
    }

    public function getDescription(): string
    {
        return 'Takes care of the hard parts of integrating UI5 with Laravel: secure CSRF handling, session-aware fetch calls, and a clean way to connect your UIComponent to a backend service—ready to use, no hassle.';
    }

    public function getVendor(): string
    {
        return 'Pragmatiqu IT GmbH';
    }
}
