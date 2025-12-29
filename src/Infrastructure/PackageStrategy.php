<?php

namespace LaravelUi5\Core\Infrastructure;

use JsonException;
use LaravelUi5\Core\Infrastructure\Contracts\Ui5SourceStrategyInterface;
use LaravelUi5\Core\Introspection\App\Ui5AppSource;
use LaravelUi5\Core\Introspection\Library\Ui5LibrarySource;

final readonly class PackageStrategy implements Ui5SourceStrategyInterface
{
    public function __construct(
        private string $srcPath
    )
    {
    }

    public function getSourcePath(): string
    {
        return $this->srcPath;
    }

    /**
     * @throws JsonException
     */
    public function createAppSource(string $vendor): Ui5AppSource
    {
        return Ui5AppSource::fromPackage($this->getSourcePath(), $vendor);
    }

    /**
     * @throws JsonException
     */
    public function createLibrarySource(string $vendor): Ui5LibrarySource
    {
        return Ui5LibrarySource::fromPackage($this->getSourcePath(), $vendor);
    }
}
