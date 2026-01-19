<?php

namespace LaravelUi5\Core\Infrastructure;

use JsonException;
use LaravelUi5\Core\Infrastructure\Contracts\Ui5SourceStrategyInterface;
use LaravelUi5\Core\Introspection\App\Ui5AppSource;
use LaravelUi5\Core\Introspection\Library\Ui5LibrarySource;
use LogicException;

final readonly class SelfContainedStrategy extends AbstractSourceStrategy
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

    public function createLibrarySource(string $vendor): Ui5LibrarySource
    {
        throw new LogicException('Self-contained libraries are not supported.');
    }
}
