<?php

namespace LaravelUi5\Core\Introspection\Library;

use JsonException;
use LaravelUi5\Core\Contracts\Ui5Descriptor;
use LaravelUi5\Core\Contracts\Ui5Source;
use LaravelUi5\Core\Introspection\Ui5Framework;
use LaravelUi5\Core\Introspection\Ui5I18n;
use LaravelUi5\Core\Introspection\Ui5PackageMeta;

final readonly class Ui5LibrarySource extends Ui5Source
{
    public function __construct(
        private string               $srcPath,
        private Ui5LibraryDescriptor $descriptor,
        private Ui5I18n              $i18n,
        private ?Ui5PackageMeta      $package = null,
        private ?Ui5Framework        $framework = null,
    )
    {
    }

    /* -- Introspection API ------------------------------------------------ */

    public function getSourcePath(): string
    {
        return $this->srcPath;
    }

    public function getPackageMeta(): ?Ui5PackageMeta
    {
        return $this->package;
    }

    public function getFramework(): ?Ui5Framework
    {
        return $this->framework;
    }

    public function getDescriptor(): Ui5LibraryDescriptor
    {
        return $this->descriptor;
    }

    public function getI18n(): Ui5I18n
    {
        return $this->i18n;
    }

    /* -- Factory ---------------------------------------------------------- */

    /**
     * @throws JsonException
     */
    public static function fromWorkspace(string $path): self
    {
        $framework = Ui5Framework::fromUi5Yaml($path);

        $package = Ui5PackageMeta::fromPackageJson($path);

        $library = Ui5LibraryDescriptor::fromLibraryXml($path, $framework->getNamespace(), $package->getBuilder());

        $i18n = Ui5I18n::fromMessageBundles($path, $framework->getNamespace());

        return new self(
            srcPath: $path,
            descriptor: $library,
            i18n: $i18n,
            package: $package,
            framework: $framework
        );
    }

    /**
     * @throws JsonException
     */
    public static function fromPackage(string $path, string $vendor): self
    {
        $library = Ui5LibraryDescriptor::fromLibraryManifest($path, $vendor);

        $i18n = Ui5I18n::fromBundles($path);

        return new self(
            srcPath: $path,
            descriptor: $library,
            i18n: $i18n,
        );
    }
}
