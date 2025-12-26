<?php

namespace LaravelUi5\Core\Commands\Concerns;

use Illuminate\Support\Facades\File;
use LogicException;
use Symfony\Component\Process\Process;

trait RunsUi5Build
{
    protected function runBuild(string $path): void
    {
        $packageJson = $path . '/package.json';

        if (!File::exists($packageJson)) {
            throw new LogicException(
                "Cannot run UI5 build: package.json not found in {$path}"
            );
        }

        $data = json_decode(
            file_get_contents($packageJson),
            true
        );

        if (!isset($data['scripts']['build'])) {
            throw new LogicException(
                "Cannot run UI5 build: no build script defined in package.json"
            );
        }

        $builder = $data['scripts']['build'];

        $process = Process::fromShellCommandline(
            $builder,
            $path
        );

        $process->setTimeout(null);
        $process->run(function ($type, $buffer) {
            echo $buffer;
        });

        if (!$process->isSuccessful()) {
            throw new LogicException('UI5 build failed.');
        }
    }
}
