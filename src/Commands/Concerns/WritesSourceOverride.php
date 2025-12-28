<?php

namespace LaravelUi5\Core\Commands\Concerns;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait WritesSourceOverride
{
    public function writeSourceOverride(string $moduleClass, string $srcPath): void
    {
        $path = base_path('.ui5-sources.php');

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
