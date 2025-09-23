<?php

namespace LaravelUi5\Core\Commands;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class GenerateUi5ReportCommand extends BaseGenerator
{
    protected $signature = 'ui5:report
                            {name : The report in the form App/Report}
                            {--php-ns-prefix=Pragmatiqu : The namespace prefix for the php package}
                            {--js-ns-prefix=io.pragmatiqu : Root namespace prefix for JS artifacts}
                            {--actions=}
                            {--title=}
                            {--description=}
                            {--formats=html,pdf}';

    protected $description = 'Create a new UI5 report artifact with all related resources';

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
        [$app, $reportName] = $this->parseCamelCasePair($name);

        if (!$this->assertAppExists($app)) {
            $this->components->error("App {$app} does not exist.");
            return self::FAILURE;
        }

        $app = Str::studly($app);
        $urlKey = Str::snake($reportName);
        $slug = Str::slug($app);
        $reportNamespace = Str::studly($reportName);
        $targetPath = base_path("ui5/{$app}/src/Reports/{$reportNamespace}");
        $resourcesPath = base_path("ui5/{$app}/resources/ui5/reports/{$urlKey}");
        $phpNamespacePrefix = rtrim($this->option('php-ns-prefix'), '\\');
        $jsPrefix = rtrim($this->option('js-ns-prefix'), '.');
        $phpNamespace = "{$phpNamespacePrefix}\\{$app}\\Reports\\$reportNamespace";
        $jsNamespace = "{$jsPrefix}.reports.{$urlKey}";

        if (File::exists("$targetPath/Report.php")) {
            $this->components->error("Ui5Report {$reportName} already exists.");
            return self::FAILURE;
        }

        File::ensureDirectoryExists($targetPath);

        $title = $this->option('title') ?? $reportNamespace;
        $description = $this->option('description') ?? 'Report generated via ui5:report';
        $formats = explode(',', $this->option('formats'));
        $actions = explode(',', $this->option('actions'));

        // Create Ui5Report
        $this->files->put("$targetPath/Report.php", $this->compileStub('Ui5Report.stub', [
            'namespace' => $phpNamespace,
            'ui5Namespace' => $jsNamespace,
            'app' => Str::kebab($app),
            'urlKey' => $urlKey,
            'slug' => $slug,
            'title' => $title,
            'description' => $description,
            'formats' => $this->formatArray($formats),
            'actionEntry' => $this->formatActionEntry($actions),
        ]));

        // Create ReportDataProvider
        $this->files->put("$targetPath/Provider.php", $this->compileStub('ReportProvider.stub', [
            'namespace' => $phpNamespace,
            'name' => 'Report'
        ]));

        // Create Actions
        foreach ($actions as $action) {
            if ('' !== $action) {
                $actionClass = "{$action}Action";
                $this->files->put("$targetPath/$actionClass.php", $this->compileStub('ReportAction.stub', [
                    'name' => $actionClass,
                    'namespace' => $phpNamespace,
                    'action' => $action
                ]));
            }
        }

        // Create UI5 blade templates
        File::ensureDirectoryExists($resourcesPath);

        $this->files->put("$resourcesPath/Report.controller.js", $this->compileStub('Report.controller.stub', [

        ]));
        $this->files->put("$resourcesPath/Report.view.xml", $this->compileStub('Report.view.stub', [

        ]));
        $this->files->put("$resourcesPath/report.blade.php", $this->compileStub('report.blade.stub', [

        ]));

        $this->components->info("UI5 Report '$reportName' and corresponding artifacts in Ui5App {$app} created successfully.");
        $this->components->info("💡 Don’t forget to register this report in your module");

        return self::SUCCESS;
    }

    protected function formatArray(array $items): string
    {
        return '[' . implode(', ', array_map(fn($i) => "'{$i}'", $items)) . ']';
    }

    protected function formatActionEntry(array $actions): string
    {
        $actionEntries = [];

        foreach ($actions as $action) {
            $action = trim($action);
            if ($action === '') continue;

            $key = Str::snake($action);               // z.B. 'discard_hours'
            $class = Str::studly($action) . 'Action'; // z.B. 'DiscardHoursAction'

            $actionEntries[] = "'$key' => {$class}::class";
        }

        return implode(",\n            ", $actionEntries);
    }
}
