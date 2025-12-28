<?php

namespace Fixtures\Hello\Reports\World;

use LaravelUi5\Core\Enums\ArtifactType;
use LaravelUi5\Core\Ui5\Contracts\DataProviderInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ReportInterface;

class Report implements Ui5ReportInterface
{

    public function __construct(private Ui5ModuleInterface $module)
    {

    }

    public function getModule(): Ui5ModuleInterface
    {
        return $this->module;
    }

    public function getNamespace(): string
    {
        return 'com.laravelui5.reports.hello-world-report';
    }

    public function getType(): ArtifactType
    {
        return ArtifactType::Report;
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }

    public function getTitle(): string
    {
        return 'World';
    }

    public function getDescription(): string
    {
        return 'Report generated via ui5:report';
    }

    public function getSlug(): string
    {
        return 'hello-world-report';
    }

    public function getProvider(): DataProviderInterface
    {
        return new Provider();
    }

    public function getSelectionViewPath(): string
    {
        return __DIR__ . '/../../../resources/ui5/reports/world/Report.view.xml';
    }

    public function getSelectionControllerPath(): string
    {
        return __DIR__ . '/../../../resources/ui5/reports/world/Report.controller.js';
    }

    public function getReportView(): string
    {
        return __DIR__ . '/../../../resources/ui5/reports/world/report.blade.php';
    }

    public function getActions(): array
    {
        return [
            'take_off' => TakeOffAction::class
        ];
    }

    public function setSlug(string $slug): void
    {
    }
}
