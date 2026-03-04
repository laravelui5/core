<?php

namespace LaravelUi5\Core\Commands;

use InvalidArgumentException;
use Illuminate\Support\Facades\File;
use Illuminate\Console\Command;
use ReflectionClass;
use ReflectionException;

class BaseGenerator extends Command
{
    private ReflectionClass $class;

    protected function assertAppExists(string $app): bool
    {
        $file = base_path("ui5/{$app}/src/{$app}Module.php");

        if (!file_exists($file)) {
            return false;
        }

        $namespace = $this->getNamespaceFromFile($file);
        $class = "{$namespace}\\{$app}Module";

        if (!class_exists($class)) {
            return false;
        }

        $this->class = new ReflectionClass($class);

        return true;
    }

    function getNamespaceFromFile(string $file): ?string
    {
        $src = file_get_contents($file);
        $tokens = token_get_all($src);

        $namespace = '';

        $count = count($tokens);

        for ($i = 0; $i < $count; $i++) {

            $token = $tokens[$i];

            if (!is_array($token)) {
                continue;
            }

            // Namespace finden
            if ($token[0] === T_NAMESPACE) {

                for ($j = $i + 1; $j < $count; $j++) {

                    if ($tokens[$j] === ';' || $tokens[$j] === '{') {
                        break;
                    }

                    if (is_array($tokens[$j])) {
                        $namespace .= $tokens[$j][1];
                    }
                }
            }
        }

        return $namespace ? trim($namespace): null;
    }

    protected function getPhpNamespacePrefix(): string
    {
        return $this->class->getNamespaceName();
    }

    /**
     * @throws ReflectionException
     */
    protected function getJsNamespacePrefix(): string
    {
        $module = $this->class->newInstanceWithoutConstructor();
        return $module->getName();
    }

    /**
     * Parses and validates a name in the format AppName/ObjectName.
     *
     * Ensures that both parts use CamelCase and that the separator is present.
     *
     * @param string $input The raw input string, e.g. "ProjectKpi/CutOff"
     * @return array{string, string} [$appName, $objectName]
     * @throws InvalidArgumentException If format or naming is incorrect
     */
    protected function parseCamelCasePair(string $input): array
    {
        if (!str_contains($input, '/')) {
            throw new InvalidArgumentException('Please use the format AppName/ObjectName (e.g. Projects/CutOff).');
        }

        [$app, $object] = explode('/', $input, 2);

        foreach (['App name' => $app, 'Object name' => $object] as $label => $value) {
            $this->assertCamelCase($label, $value);
        }

        return [$app, $object];
    }

    protected function assertCamelCase(string $label, string $input): void
    {
        if (!preg_match('/^[A-Z][a-zA-Z0-9]+$/', $input)) {
            throw new InvalidArgumentException("{$label} must be CamelCase (e.g. ProjectKpi, CutOff).");
        }
    }

    /**
     * Compiles a stub file by replacing all placeholders with the provided values.
     *
     * This method loads a stub file from the predefined `resources/stubs` directory
     * and replaces all placeholder tokens (e.g., `{{ className }}`) with the corresponding
     * values from the `$replacements` array.
     *
     * @param string $stubName The name of the stub file (e.g., 'TileClass.stub').
     * @param array<string, string> $replacements An associative array of placeholder names
     *                                            and their replacement values.
     *                                            Keys should match the placeholder names
     *                                            without curly braces.
     *
     * @return string The compiled stub content with all placeholders replaced.
     */
    protected function compileStub(string $stubName, array $replacements): string
    {
        $stub = File::get(__DIR__ . "/../../resources/stubs/$stubName");

        foreach ($replacements as $key => $value) {
            $stub = str_replace("{{ $key }}", $value, $stub);
        }

        return $stub;
    }

    /**
     * @param string $sourcePath The distribution directory of the UI5 resources
     * @param string $targetPath The src path of the LaravelUi5 module, e.g. ./ui5/Users/src
     * @param array $toCopy Array with file names to copy
     * @return void
     */
    protected function copyDistAssets(string $sourcePath, string $targetPath, array $toCopy): void
    {
        $source = $sourcePath;
        $target = $targetPath . '../resources/ui5/';

        File::ensureDirectoryExists($target);
        File::ensureDirectoryExists($target . 'i18n');

        foreach ($toCopy as $file) {
            $from = "{$source}/{$file}";
            $to = $target . $file;
            if (file_exists($from)) {
                File::copy($from, $to);
                if ($this->output->isVerbose()) {
                    $this->info("✓ {$file}");
                }
            }
        }
    }
}
