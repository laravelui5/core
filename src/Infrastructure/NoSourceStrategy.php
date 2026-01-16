<?php

namespace LaravelUi5\Core\Infrastructure;

use LaravelUi5\Core\Infrastructure\Contracts\Ui5SourceStrategyInterface;
use LaravelUi5\Core\Introspection\App\Ui5AppSource;
use LaravelUi5\Core\Introspection\Library\Ui5LibrarySource;
use LogicException;

class NoSourceStrategy implements Ui5SourceStrategyInterface
{
    public function getSourcePath(): string
    {
        throw new LogicException('No source introspection provided.');
    }

    public function createAppSource(string $vendor): Ui5AppSource
    {
        throw new LogicException('No source introspection provided.');
    }

    public function createLibrarySource(string $vendor): Ui5LibrarySource
    {
        throw new LogicException('No source introspection provided.');
    }
}
