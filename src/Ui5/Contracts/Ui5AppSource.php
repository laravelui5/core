<?php

namespace LaravelUi5\Core\Ui5\Contracts;

use JsonException;
use LaravelUi5\Core\Contracts\Ui5Descriptor;
use LaravelUi5\Core\Contracts\Ui5Source;

final readonly class Ui5AppSource extends Ui5Source
{
    public function __construct(
        private string          $srcPath,
        private Ui5Descriptor   $descriptor,
        private Ui5I18n         $i18n,
        private bool            $isDev,
        private ?Ui5PackageMeta $package = null,
        private ?Ui5Framework   $framework = null,
        private ?Ui5Bootstrap   $bootstrap = null,
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

    public function getDescriptor(): Ui5Descriptor
    {
        return $this->descriptor;
    }

    public function getBootstrap(): ?Ui5Bootstrap
    {
        return $this->bootstrap;
    }

    public function getI18n(): Ui5I18n
    {
        return $this->i18n;
    }

    public function isDev(): bool
    {
        return $this->isDev;
    }

    /* -- Factory ---------------------------------------------------------- */

    /**
     * @throws JsonException
     */
    public static function fromWorkspace(string $path, string $vendor, bool $isDev = false): Ui5AppSource
    {
        $base = $isDev ? 'webapp' : 'dist';

        $actualPath = "{$path}/{$base}";

        $framework = Ui5Framework::fromUi5Yaml($path);

        $package = Ui5PackageMeta::fromPackageJson($path);

        $i18n = Ui5I18n::fromI18nProperties($actualPath);

        $bootstrap = Ui5Bootstrap::fromIndexHtml($actualPath);

        $descriptor = Ui5AppDescriptor::fromManifestJson($actualPath, $i18n, $vendor);

        return new self(
            srcPath: $path,
            descriptor: $descriptor,
            i18n: $i18n,
            isDev: $isDev,
            package: $package,
            framework: $framework,
            bootstrap: $bootstrap
        );
    }

    /**
     * @throws JsonException
     */
    public static function fromPackage(string $path, string $vendor): Ui5AppSource
    {
        $i18n = Ui5I18n::fromI18nProperties($path);

        $descriptor = Ui5AppDescriptor::fromManifestJson($path, $i18n, $vendor);

        return new self(
            srcPath: $path,
            descriptor: $descriptor,
            i18n: $i18n,
            isDev: false,
        );
    }
}
