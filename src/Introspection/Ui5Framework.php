<?php

namespace LaravelUi5\Core\Introspection;

use Illuminate\Support\Facades\File;
use LogicException;
use Symfony\Component\Yaml\Yaml;

final readonly class Ui5Framework
{
    public function __construct(
        private string $name,
        private string $version,
        private string $namespace,
    )
    {
    }

    /* -- API -------------------------------------------------------------- */

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /* -- Factory ---------------------------------------------------------- */

    public static function fromUi5Yaml(string $path): self
    {
        $ui5Yaml = $path . '/ui5.yaml';

        if (!File::exists($ui5Yaml)) {
            throw new LogicException("ui5.yaml not found in {$path}");
        }

        $yaml = Yaml::parseFile($ui5Yaml);

        $namespace = $yaml['metadata']['name'] ?? null;
        if (!$namespace) {
            throw new LogicException("Missing metadata.name in ui5.yaml");
        }

        return new self(
            name: $yaml['framework']['name'] ?? 'OpenUI5',
            version: $yaml['framework']['version'] ?? '1.136.0',
            namespace: $namespace,
        );
    }
}
