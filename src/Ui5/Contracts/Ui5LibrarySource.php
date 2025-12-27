<?php

namespace LaravelUi5\Core\Ui5\Contracts;

use JsonException;
use LaravelUi5\Core\Contracts\Ui5Descriptor;
use LaravelUi5\Core\Contracts\Ui5Source;

final readonly class Ui5LibrarySource extends Ui5Source
{
    public function __construct(
        private string               $srcPath,
        private Ui5PackageMeta       $package,
        private Ui5Framework         $framework,
        private Ui5LibraryDescriptor $descriptor,
    )
    {
    }

    /* -- Introspection API ------------------------------------------------ */

    public function getSourcePath(): string
    {
        return $this->srcPath;
    }

    public function getPackageMeta(): Ui5PackageMeta
    {
        return $this->package;
    }

    public function getFramework(): Ui5Framework
    {
        return $this->framework;
    }

    public function getDescriptor(): Ui5Descriptor
    {
        return $this->descriptor;
    }

    /* -- Factory ---------------------------------------------------------- */

    /**
     * @throws JsonException
     */
    public static function fromFilesystem(string $path): self
    {
        $framework = Ui5Framework::fromUi5Yaml($path);

        $package = Ui5PackageMeta::fromPackageJson($path);

        $library = Ui5LibraryDescriptor::fromLibraryXml($path, $framework->namespace, $package->builder);

        return new self(
            srcPath: $path,
            package: $package,
            framework: $framework,
            descriptor: $library
        );
    }
}
