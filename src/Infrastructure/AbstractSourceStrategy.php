<?php

namespace LaravelUi5\Core\Infrastructure;

use LaravelUi5\Core\Infrastructure\Contracts\Ui5SourceStrategyInterface;
use LaravelUi5\Core\Introspection\App\Ui5AppSource;
use LaravelUi5\Core\Introspection\Library\Ui5LibrarySource;

abstract readonly class AbstractSourceStrategy implements Contracts\Ui5SourceStrategyInterface
{
    public function resolvePath(string $path): string
    {
        return $this->getSourcePath() . "/{$path}";
    }
}
