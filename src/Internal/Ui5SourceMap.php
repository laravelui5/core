<?php

namespace LaravelUi5\Core\Internal;

use Illuminate\Support\Facades\File;
use JsonException;
use LaravelUi5\Core\Contracts\Ui5Source;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppSource;
use LaravelUi5\Core\Ui5\Contracts\Ui5LibrarySource;
use LogicException;

final readonly class Ui5SourceMap
{
    private function __construct(
        private array $modules
    )
    {
    }

    public static function addOrUpdate(
        string $module,
        string $type,
        string $srcPath,
        string $vendor,
    ): void
    {
        $path = base_path('.ui5-sources.php');

        $config = File::exists($path)
            ? require $path
            : ['modules' => []];

        $config['modules'][$module] = [
            'type' => $type,
            'path' => $srcPath,
            'vendor' => $vendor,
        ];

        File::put($path, self::export($config));
    }

    private static function export(array $config): string
    {
        $data = var_export($config, true);
        return <<<PHP
<?php
return {$data};
PHP;
    }

    public static function load(string $path): self
    {
        if (!File::exists($path)) {
            return new self([]);
        }

        $config = require $path;

        return new self(
            $config['modules'] ?? []
        );
    }

    /**
     * @throws JsonException
     */
    public function forModule(string $name): ?Ui5Source
    {
        if (!isset($this->modules[$name])) {
            return null;
        }

        $entry = $this->modules[$name];

        $this->assertKey('path', $entry, $name);
        $this->assertKey('type', $entry, $name);
        $this->assertKey('vendor', $entry, $name);

        $rootPath = base_path($entry['path']);

        return match ($entry['type']) {
            'app' => Ui5AppSource::fromFilesystem($rootPath, $entry['vendor'], !app()->runningInConsole()),
            'library' => Ui5LibrarySource::fromFilesystem($rootPath),
            default => null,
        };
    }

    private function assertKey(string $key, array $config, string $name): void
    {
        if (!array_key_exists($key, $config)) {
            throw new LogicException(sprintf(
                'Missing key `%s` in .ui5-sources.php for module %s',
                $key,
                $name
            ));
        }
    }
}
