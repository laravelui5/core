<?php

namespace Tests\Fixture\Hello\Reports\World;

use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ReportInterface;
use LaravelUi5\Core\Ui5\Contracts\ReportDataProviderInterface;
use LaravelUi5\Core\Enums\ArtifactType;

class Report implements Ui5ReportInterface
{

    public function __construct(protected Ui5ModuleInterface $module)
    {
    }

    public function getModule(): ?Ui5ModuleInterface
    {
        return $this->module;
    }

    public function getNamespace(): string
    {
        return 'io.pragmatiqu.hello.reports.world';
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
        return 'world';
    }

    public function getProvider(): ReportDataProviderInterface
    {
        return new Provider();
    }

    public function getViewPath(): string
    {
        return __DIR__ . '/../../../resources/ui5/reports/world/Report.view.xml';
    }

    public function getControllerPath(): string
    {
        return __DIR__ . '/../../../resources/ui5/reports/world/Report.controller.js';
    }

    public function getReportView(): string
    {
        return __DIR__ . '/../../../resources/ui5/reports/world/report.blade.php';
    }

    public function getSupportedFormats(): array
    {
        return ['html', 'pdf'];
    }

    public function getActions(): array
    {
        return [
            'take_off' => TakeOffAction::class
        ];
    }
}
