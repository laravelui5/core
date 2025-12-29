<?php

namespace LaravelUi5\Core\Introspection;

use JsonException;
use LogicException;

final readonly class Ui5PackageMeta
{
    public function __construct(
        private string $name,
        private string $version,
        private string $builder,
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

    public function getBuilder(): string
    {
        return $this->builder;
    }

    /* -- Factory ---------------------------------------------------------- */

    /**
     * @throws JsonException
     */
    public static function fromPackageJson(string $path): self
    {
        $srcPath = "{$path}/package.json";

        if (!file_exists($srcPath)) {
            throw new LogicException("package.json not found at {$srcPath}");
        }

        $data = json_decode(
            file_get_contents($srcPath),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        return new self(
            name: $data['name'] ?? 'unknown',
            version: $data['version'] ?? '0.0.0',
            builder: $data['scripts']['build'] ?? 'ui5 build --clean-dest'
        );
    }
}
