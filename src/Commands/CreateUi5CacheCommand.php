<?php

namespace LaravelUi5\Core\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use LaravelUi5\Core\Enums\ArtifactType;
use LaravelUi5\Core\Ui5\Contracts\SluggableInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;

class CreateUi5CacheCommand extends Command
{
    protected $signature = 'ui5:cache';

    protected $description = 'Caches all configured UI5 modules and artifacts into a fast runtime cache file.';

    public function handle(): int
    {
        $config = config('ui5');
        $modules = $config['modules'] ?? [];
        $dashboards = $config['dashboards'] ?? [];
        $reports = $config['reports'] ?? [];

        $moduleCache = [];
        $artifactCache = [];
        $namespaceToModule = [];
        $artifactToModule = [];
        $slugs = [];

        foreach ($modules as $slug => $moduleClass) {
            $this->line("• Module <info>{$slug}</info> → {$moduleClass}");

            /** @var Ui5ModuleInterface $module */
            $module = new $moduleClass($slug);
            $moduleCache[$slug] = $moduleClass;

            // Register all artifacts provided by this module
            $artifacts = array_filter([
                $module->hasApp() ? $module->getApp() : null,
                $module->hasLibrary() ? $module->getLibrary() : null,
                ...$module->getCards(),
                ...$module->getKpis(),
                ...$module->getTiles(),
                ...$module->getActions(),
                ...$module->getResources(),
            ]);

            foreach ($artifacts as $artifact) {
                if (!$artifact instanceof Ui5ArtifactInterface) {
                    continue;
                }

                $ns = $artifact->getNamespace();
                $class = get_class($artifact);

                $artifactCache[$ns] = $class;
                $namespaceToModule[$ns] = $slug;
                $artifactToModule[$class] = $slug;

                if ($artifact instanceof SluggableInterface) {
                    $urlKey = ArtifactType::urlKeyFromArtifact($artifact, $slug);
                    $slugs[$urlKey] = $class;
                }
            }
        }

        $this->mapClasses($dashboards, $slugs);
        $this->mapClasses($reports, $slugs);

        // Final structure
        $data = [
            'modules' => $moduleCache,
            'artifacts' => $artifactCache,
            'namespaceToModule' => $namespaceToModule,
            'artifactToModule' => $artifactToModule,
            'slugs' => $slugs,
        ];

        $this->writeCacheFile($data);
        $this->components->info('UI5 cache file created: bootstrap/cache/ui5.php');

        return self::SUCCESS;
    }

    protected function mapClasses(array $classes, array &$slugs): void
    {
        foreach ($classes as $class) {
            $instance = new $class;
            if (!$instance instanceof Ui5ArtifactInterface) {
                continue;
            }

            $ns = $instance->getNamespace();
            $artifactCache[$ns] = $class;
            if ($instance instanceof SluggableInterface) {
                $urlKey = ArtifactType::urlKeyFromArtifact($instance, null);
                $slugs[$urlKey] = $class;
            }
        }
    }

    protected function writeCacheFile(array $data): void
    {
        $filesystem = new Filesystem();
        $path = base_path('bootstrap/cache/ui5.php');

        $filesystem->put(
            $path,
            '<?php return ' . var_export($data, true) . ';' . PHP_EOL
        );
    }
}
