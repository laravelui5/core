<?php

namespace Tests\Fixture\Hello;

use LaravelUi5\Core\Ui5\Contracts\Ui5LibraryInterface;
use LaravelUi5\Core\Enums\ArtifactType;
use LaravelUi5\Core\Traits\HasAssetsTrait;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;

class HelloLibrary implements Ui5LibraryInterface
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
        return 'com.laravelui5.hello';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }

    public function getTitle(): string
    {
        return 'Laravel Ui5 Hello Library';
    }

    public function getDescription(): string
    {
        return 'Some description here…';
    }

    public function getVendor(): string
    {
        return 'Pragmatiqu IT GmbH';
    }
}
