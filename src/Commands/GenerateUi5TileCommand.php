<?php

namespace LaravelUi5\Core\Commands;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class GenerateUi5TileCommand extends BaseGenerator
{
    protected $signature = 'ui5:tile
        {name : Tile name in App/Tile format (e.g. Offers/ProjectKpi)}
        {--title= : The title of the tile}
        {--description= : The description of the tile}';

    protected $description = 'Create a new UI5 tile artifact with related resources';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * @throws Exception
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        [$app, $tile] = $this->parseCamelCasePair($name);

        if (!$this->assertAppExists($app)) {
            $this->components->error("App {$app} does not exist.");
            return self::FAILURE;
        }

        $phpPrefix = $this->getPhpNamespacePrefix();
        $jsPrefix = $this->getJsNamespacePrefix();
        $urlKey = Str::snake($tile);
        $namespace = "{$phpPrefix}\\Tiles";
        $jsNamespace = "{$jsPrefix}.tiles.{$urlKey}";
        $title = $this->option('title') ?? $tile;
        $description = $this->option('description') ?? 'Tile generated via ui5:tile';

        $targetPath = base_path("ui5/{$app}/src/Tiles");
        if (File::exists("$targetPath/{$tile}Tile.php")) {
            $this->components->error("Tile {$tile} already exists in Ui5App module {$app}.");
            return self::FAILURE;
        }

        File::ensureDirectoryExists("{$targetPath}/Provider");

        // Create Ui5Tile
        $this->files->put("$targetPath/{$tile}Tile.php", $this->compileStub('Ui5Tile.stub', [
            'namespace' => $namespace,
            'class' => $tile,
            'ui5Namespace' => $jsNamespace,
            'urlKey' => $urlKey,
            'title' => $title,
            'description' => $description,
        ]));

        // Create DataProvider
        $this->files->put("$targetPath/Provider/{$tile}Provider.php", $this->compileStub('TileProvider.stub', [
            'namespace' => $namespace,
            'class' => $tile,
        ]));

        $this->components->success("UI5 Tile '$tile' created successfully.");
        $this->components->info("💡 Don’t forget to register this card in your module");

        return self::SUCCESS;
    }
}
