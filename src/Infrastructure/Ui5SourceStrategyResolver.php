<?php

namespace LaravelUi5\Core\Infrastructure;

use LaravelUi5\Core\Infrastructure\Contracts\Ui5SourceOverrideStoreInterface;
use LaravelUi5\Core\Infrastructure\Contracts\Ui5SourceStrategyInterface;
use LaravelUi5\Core\Infrastructure\Contracts\Ui5SourceStrategyResolverInterface;
use LogicException;
use ReflectionClass;

class Ui5SourceStrategyResolver implements Ui5SourceStrategyResolverInterface
{
    public function __construct(protected Ui5SourceOverrideStoreInterface $sourceStore)
    {
    }

    public function resolve(string $moduleClass): Ui5SourceStrategyInterface
    {
        if ($path = $this->sourceStore->get($moduleClass)) {
            return new WorkspaceStrategy($path);
        }

        $ref = new ReflectionClass($moduleClass);

        $moduleDir = dirname($ref->getFileName());

        // Convention for packaged UI5 projects:
        // ui5/<Module>/src/ → ui5/<Module>/resources/ui5
        $packagePath = realpath($moduleDir . '/../resources/ui5');

        if ($packagePath && is_dir($packagePath)) {
            return new PackageStrategy($packagePath);
        }

        // Convention for self-contained apps:
        // ui5/<Module>/src/ → ui5/<Module>/resources/app
        $packagePath = realpath($moduleDir . '/../resources/app');
        if ($packagePath && is_dir($packagePath)) {
            return new SelfContainedStrategy($packagePath);
        }

        // packaged core apps
        $packagePath = realpath($moduleDir . '/../resources/dashboard-app');
        if ($packagePath && is_dir($packagePath)) {
            return new SelfContainedStrategy($packagePath);
        }

        $packagePath = realpath($moduleDir . '/../resources/report-app');
        if ($packagePath && is_dir($packagePath)) {
            return new SelfContainedStrategy($packagePath);
        }

        throw new LogicException(
            "Unable to resolve UI5 source path for module [{$moduleClass}] from {$moduleDir}."
        );
    }
}
