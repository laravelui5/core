<?php

namespace LaravelUi5\Core\Introspection\Library;

use Illuminate\Support\Facades\File;
use JsonException;
use LaravelUi5\Core\Contracts\Ui5Descriptor;
use LogicException;
use SimpleXMLElement;

/**
 * Represents the descriptor of a UI5 library as derived from the `.library` file.
 *
 * Note on naming:
 * ----------------
 * In UI5, the XML element `<library><name>` does NOT represent a human-readable
 * name, but the technical UI5 namespace of the library (e.g. `com.laravelui5.core`).
 *
 * SAP historically uses the term "name" for this value, even though it semantically
 * represents a namespace. To preserve this distinction and make the origin explicit,
 * this descriptor intentionally stores the raw value as `$name`, while exposing it
 * via {@see getNamespace()}.
 *
 * This makes the SAP-originated terminology visible at the boundary, while providing
 * a clear and consistent semantic API to consumers.
 */
final readonly class Ui5LibraryDescriptor extends Ui5Descriptor
{
    public function __construct(
        private string $name,
        private string $vendor,
        private string $version,
        private string $title,
        private string $documentation,
        private array  $dependencies
    )
    {
    }

    /* -- Introspection API ------------------------------------------------ */

    public function getNamespace(): string
    {
        return $this->name;
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
        return $this->documentation;
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

    public static function fromLibraryXml(string $path, string $namespace, string $builder): self
    {
        $srcPath = $path
            . '/dist/resources/'
            . str_replace('.', '/', $namespace)
            . '/.library';

        if (!File::exists($srcPath)) {
            throw new LogicException("Missing .library file at {$path}. Run `$builder` first.");
        }

        $xml = simplexml_load_file($srcPath);

        if (!$xml instanceof SimpleXMLElement) {
            throw new LogicException("Invalid .library XML at {$srcPath}");
        }

        $dependencies = [];

        if (isset($xml->dependencies->dependency)) {
            foreach ($xml->dependencies->dependency as $dep) {
                $dependencies[] = (string)$dep->libraryName;
            }
        }

        return new self(
            name: (string)$xml->name,
            vendor: (string)$xml->vendor,
            version: (string)$xml->version,
            title: (string)$xml->title,
            documentation: (string)$xml->documentation,
            dependencies: $dependencies
        );
    }

    /**
     * @throws JsonException
     */
    public static function fromLibraryManifest(string $path, string $vendor): self
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

        if (!isset($manifest['sap.app']['id'])) {
            throw new LogicException('Invalid manifest.json: missing sap.app.id');
        }

        $namespace = $manifest['sap.app']['id'];

        $title = $manifest['sap.app']['title'] ?? 'Missing title';

        $description = $manifest['sap.app']['description'] ?? 'Missing description';

        $version = $manifest['sap.app']['applicationVersion']['version'] ?? '0.0.0';

        // Dependencies: sap.ui5.dependencies.libs (keys only!)
        $libs = $manifest['sap.ui5']['dependencies']['libs'] ?? [];
        if (!is_array($libs)) {
            throw new LogicException('Invalid manifest.json: sap.ui5.dependencies.libs must be an object');
        }

        $dependencies = array_keys($libs);

        return new self(
            name: $namespace,
            vendor: $vendor,
            version: $version,
            title: $title,
            documentation: $description,
            dependencies: $dependencies
        );
    }
}
