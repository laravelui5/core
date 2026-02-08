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
                            {--title=}
                            {--description=}';

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
        $targetPath = base_path("ui5/{$app}/src/Reports");
        $resourcesPath = base_path("ui5/{$app}/resources/ui5/reports/{$urlKey}");
        $phpNamespacePrefix = $this->getPhpNamespacePrefix();
        $jsNamespacePrefix = $this->getJsNamespacePrefix();
        $phpNamespace = "{$phpNamespacePrefix}\\Reports";
        $jsNamespace = "{$jsNamespacePrefix}.reports.{$urlKey}";

        if (File::exists("$targetPath/{$reportName}Report.php")) {
            $this->components->error("Ui5Report {$reportName} already exists.");
            return self::FAILURE;
        }

        File::ensureDirectoryExists("{$targetPath}/Provider");

        $title = $this->option('title') ?? $reportNamespace;
        $description = $this->option('description') ?? 'Report generated via ui5:report';

        // Create Ui5Report
        $this->files->put("$targetPath/{$reportName}Report.php", $this->compileStub('Ui5Report.stub', [
            'namespace' => $phpNamespace,
            'ui5Namespace' => $jsNamespace,
            'className' => "{$reportName}Report",
            'providerName' => "{$reportName}Provider",
            'app' => Str::kebab($app),
            'urlKey' => $urlKey,
            'slug' => $slug,
            'title' => $title,
            'description' => $description,
        ]));

        // Create ReportDataProvider
        $this->files->put("$targetPath/Provider/{$reportName}Provider.php", $this->compileStub('ReportProvider.stub', [
            'namespace' => $phpNamespace,
            'className' => "{$reportName}Provider",
            'name' => 'Report'
        ]));

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
}
