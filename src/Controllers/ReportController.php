<?php

namespace LaravelUi5\Core\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use LaravelUi5\Core\Contracts\Ui5Context;
use LaravelUi5\Core\Services\ExecutableHandler;
use Maatwebsite\Excel\Excel as ExcelType;
use Maatwebsite\Excel\Facades\Excel;
use LaravelUi5\Core\Ui5\Contracts\ExportInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ReportInterface;
use Spatie\LaravelPdf\PdfBuilder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use function Spatie\LaravelPdf\Support\pdf;

class ReportController
{
    public function __invoke(
        Request           $request,
        Ui5Context        $context,
        ExecutableHandler $handler,
        string            $module,
        string            $slug
    ): Factory|View|Application|BinaryFileResponse|PdfBuilder
    {
        /** @var Ui5ReportInterface $report */
        $report = $context->artifact;

        $provider = $report->getProvider();

        $data = $handler->run($provider);

        $format = $request->query('format', 'html');

        // HTML
        if ('html' === $format) {
            return view($provider->getReportName(), $data);
        }

        // XLSX
        if ('xlsx' === $format && $provider instanceof ExportInterface) {
            return Excel::download($provider, $provider->getExportName(), ExcelType::XLSX);
        }

        // default: PDF
        return pdf()
            ->view($provider->getReportName(), $data)
            ->name($provider->getReportName());
    }
}
