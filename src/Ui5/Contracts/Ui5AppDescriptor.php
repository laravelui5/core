<?php

namespace LaravelUi5\Core\Ui5\Contracts;

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
        private array  $dependencies,
        private array  $routes,
        private array  $targets,
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

    /** @return Ui5Route[] */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /** @return array<string, Ui5Target> keyed by target name */
    public function getTargets(): array
    {
        return $this->targets;
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

        $routing = $manifest['sap.ui5']['routing'] ?? [];
        $routes = [];
        foreach ($routing['routes'] ?? [] as $route) {
            if (!isset($route['name'], $route['pattern'], $route['target'])) {
                continue;
            }

            $routes[] = new Ui5Route(
                name: $route['name'],
                pattern: $route['pattern'],
                target: $route['target']
            );
        }

        $targets = [];

        foreach ($routing['targets'] ?? [] as $key => $target) {
            if (!isset($target['viewName']) && !isset($target['name'])) {
                continue;
            }

            $targets[] = new Ui5Target(
                key: $key,
                name: $target['viewName'] ?? $target['name'],
            );
        }

        return new self(
            namespace: $namespace,
            version: $version,
            title: $i18n->getTitle(),
            description: $i18n->getDescription(),
            vendor: $vendor,
            dependencies: $dependencies,
            routes: $routes,
            targets: $targets,
        );
    }
}
