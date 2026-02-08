<?php

namespace LaravelUi5\Core\Commands;

use Exception;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateUi5CardCommand extends BaseGenerator
{
    protected $signature = 'ui5:card
                            {name : The card in the form App/Card}
                            {--php-ns-prefix=Pragmatiqu : The prefix for the php package}
                            {--js-ns-prefix=io.pragmatiqu : The prefix for the js package}
                            {--title= : The title of the card}
                            {--description= : The description of the card}';

    protected $description = 'Generates a new UI5 Card including provider and manifest template';

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
        [$app, $card] = $this->parseCamelCasePair($name);

        if (!$this->assertAppExists($app)) {
            $this->components->error("App {$app} does not exist.");
            return self::FAILURE;
        }

        $phpNamespacePrefix = rtrim($this->option('php-ns-prefix'), '\\');;
        $jsNamespacePrefix = rtrim($this->option('js-ns-prefix'), '.');

        $root = base_path("ui5/{$app}");
        $src = "{$root}/src/Cards/";
        $res = "{$root}/resources/ui5/cards";

        $providerClass = "{$card}Provider";
        $slug = Str::kebab(Str::replaceLast('Card', '', $card));
        $ui5Namespace = $jsNamespacePrefix . '.' . Str::kebab($app) . '.' . $slug;

        $phpCardNamespace = "{$phpNamespacePrefix}\\{$app}\\Cards";
        $phpProviderNamespace = "{$phpNamespacePrefix}\\{$app}\\Cards\\Provider";

        if (File::exists("{$src}/{$card}Card.php")) {
            $this->components->error("Ui5Card {$card} already exists.");
            return self::FAILURE;
        }

        // Create directories
        File::ensureDirectoryExists($src);
        File::ensureDirectoryExists($res);

        // Stub: Card class
        $this->files->put("{$src}/Card.php", $this->compileStub('Ui5Card.stub', [
            'phpCardNamespace' => $phpCardNamespace,
            'phpProviderNamespace' => $phpProviderNamespace,
            'providerClass' => $providerClass,
            'ui5Namespace' => $ui5Namespace,
            'urlKey' => $slug,
            'title' => $this->option('title') ?? Str::headline(Str::replaceLast('Card', '', $card)),
            'description' => $this->option('description') ?? "Displays key data for " . Str::headline(Str::replaceLast('Card', '', $card)) . ".",
        ]));

        // Stub: Provider class
        $this->files->put("{$src}/Provider.php", $this->compileStub('CardProvider.stub', [
            'phpProviderNamespace' => $phpProviderNamespace,
        ]));

        // Stub: manifest.blade.php
        $this->files->put("{$res}/{$slug}.blade.php", $this->compileStub('CardManifest.stub', [
            'urlKey' => $slug,
            'version' => '1.0.0',
            'title' => $this->option('title') ?? Str::headline(Str::replaceLast('Card', '', $card)),
            'subTitle' => $this->option('description') ?? 'Optional Subtitle',
        ]));

        $this->components->info("Generated UI5Card {$card} and corresponding artifacts in Ui5App {$app}.");
        $this->components->info("💡 Don’t forget to register this card in your module");
        return self::SUCCESS;
    }
}
