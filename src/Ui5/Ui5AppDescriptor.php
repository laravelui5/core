<?php

namespace LaravelUi5\Core\Ui5;

use JsonException;
use LaravelUi5\Core\Contracts\Ui5Descriptor;
use LogicException;

final readonly class Ui5AppDescriptor extends Ui5Descriptor
{
    public function __construct(
        private string $namespace,
        private string $version,
        private string $title,
        private string $description,
        private string $vendor,
        private array  $dependencies
    )
    {
    }

    /* -- Introspection API ------------------------------------------------ */

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getVendor(): string
    {
        return $this->vendor;
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    /* -- Factory ---------------------------------------------------------- */

    /**
     * @throws JsonException
     */
    public static function fromManifestJson(string $path, Ui5I18n $i18n, string $vendor): self
    {
        $manifestPath = "{$path}/manifest.json";

        if (!is_file($manifestPath)) {
            throw new LogicException("manifest.json not found at {$manifestPath}");
        }

        $manifest = json_decode(
            file_get_contents($manifestPath),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $sapApp = $manifest['sap.app'] ?? null;
        if (!$sapApp || !isset($sapApp['id'])) {
            throw new LogicException('Invalid manifest.json: missing sap.app.id');
        }

        $namespace = $sapApp['id'];

        $version = $sapApp['applicationVersion']['version'] ?? '0.0.0';

        // Dependencies: sap.ui5.dependencies.libs (keys only!)
        $libs = $manifest['sap.ui5']['dependencies']['libs'] ?? [];
        if (!is_array($libs)) {
            throw new LogicException('Invalid manifest.json: sap.ui5.dependencies.libs must be an object');
        }

        $dependencies = array_keys($libs);

        return new self(
            namespace: $namespace,
            version: $version,
            title: $i18n->getTitle(),
            description: $i18n->getDescription(),
            vendor: $vendor,
            dependencies: $dependencies
        );
    }
}
