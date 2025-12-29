<?php

namespace LaravelUi5\Core\Contracts;

use LaravelUi5\Core\Ui5\Contracts\Ui5Framework;
use LaravelUi5\Core\Ui5\Contracts\Ui5I18n;
use LaravelUi5\Core\Ui5\Contracts\Ui5PackageMeta;

/**
 * Represents an introspection-capable UI5 source.
 *
 * A Ui5Source provides structured access to metadata extracted
 * from a UI5 project or a packaged descriptor.
 *
 * Not all metadata is guaranteed to be available in all contexts
 * (e.g. packaged modules vs. workspace projects).
 */
abstract readonly class Ui5Source
{
    public abstract function getSourcePath(): string;

    public abstract function getPackageMeta(): ?Ui5PackageMeta;

    public abstract function getFramework(): ?Ui5Framework;

    public abstract function getDescriptor(): Ui5Descriptor;

    public abstract function getI18n(): Ui5I18n;
}
