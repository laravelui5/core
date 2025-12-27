<?php

namespace LaravelUi5\Core\Ui5\Contracts;

use JsonException;

final readonly class Ui5PackageMeta
{
    public function __construct(
        public string $name,
        public string $version,
        public string $builder,
    ) {}

    /**
     * @throws JsonException
     */
    public static function fromPackageJson(string $path): self
    {
        $srcPath = "{$path}/package.json";

        if (!file_exists($srcPath)) {
            throw new \LogicException("package.json not found at {$srcPath}");
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
