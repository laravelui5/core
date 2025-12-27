<?php

namespace LaravelUi5\Core\Contracts;

use LaravelUi5\Core\Ui5\Contracts\Ui5Framework;
use LaravelUi5\Core\Ui5\Contracts\Ui5PackageMeta;

abstract readonly class Ui5Source
{
    public abstract function getSourcePath(): string;

    public abstract function getPackageMeta(): Ui5PackageMeta;

    public abstract function getFramework(): Ui5Framework;

    public abstract function getDescriptor(): Ui5Descriptor;
}
