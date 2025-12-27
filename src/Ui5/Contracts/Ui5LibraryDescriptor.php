<?php

namespace LaravelUi5\Core\Ui5\Contracts;

use Illuminate\Support\Facades\File;
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
            throw new LogicException("Missing .library file. Run `$builder` first.");
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
}
