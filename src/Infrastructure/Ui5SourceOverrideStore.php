<?php

namespace LaravelUi5\Core\Infrastructure;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use LaravelUi5\Core\Infrastructure\Contracts\Ui5SourceOverrideStoreInterface;

class Ui5SourceOverrideStore implements Ui5SourceOverrideStoreInterface
{
    protected string $path;

    /**
     * @var array<class-string, string>
     */
    protected array $overrides = [];

    public function __construct(string $path = '.ui5-sources.php')
    {
        $this->path = $path;

        $this->loadSourceOverrides();
    }

    protected function loadSourceOverrides(): void
    {
        $path = base_path($this->path);

        if (!is_file($path)) {
            return;
        }

        $config = require $path;

        $modules = $config['modules'] ?? [];

        $overrides = [];

        foreach ($modules as $moduleClass => $relativePath) {
            if (!is_string($moduleClass) || !is_string($relativePath)) {
                continue;
            }

            $absolutePath = base_path($relativePath);

            if (is_dir($absolutePath)) {
                $overrides[$moduleClass] = $absolutePath;
            }
        }

        $this->overrides = $overrides;
    }

    public function all(): array
    {
        return $this->overrides;
    }

    public function get(string $moduleClass): ?string
    {
        if (isset($this->overrides[$moduleClass])) {
            return $this->overrides[$moduleClass];
        }

        return null;
    }

    public function put(string $moduleClass, string $srcPath): void
    {
        $path = base_path($this->path);

        $config = File::exists($path)
            ? require $path
            : ['modules' => []];

        $config['modules'][$moduleClass] = $this->relativePath($srcPath);

        File::put($path, $this->export($config));
    }

    private function export(array $config): string
    {
        $data = preg_replace(
            ['/array \(/', '/\)(,?)/'],
            ['[', ']$1'],
            var_export($config, true)
        );
        return <<<PHP
<?php
return {$data};
PHP;
    }

    private function relativePath(string $path): string
    {
        return Str::after($path, base_path() . DIRECTORY_SEPARATOR);
    }
}
