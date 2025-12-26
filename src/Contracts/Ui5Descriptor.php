<?php

namespace LaravelUi5\Core\Contracts;

abstract readonly class Ui5Descriptor
{
    public abstract function getNamespace(): string;

    public abstract function getVersion(): string;

    public abstract function getTitle(): string;

    public abstract function getDescription(): string;

    public abstract function getVendor(): string;

    public abstract function getDependencies(): array;
}
