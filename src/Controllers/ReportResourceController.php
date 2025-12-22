<?php

namespace LaravelUi5\Core\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use LaravelUi5\Core\Contracts\Ui5CoreContext;
use LaravelUi5\Core\Ui5\Contracts\Ui5ReportInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReportResourceController
{
    public function __invoke(Ui5CoreContext $context, string $slug, string $extension): Response
    {
        /** @var Ui5ReportInterface $report */
        $report = $context->artifact;

        if (null === $report) {
            throw new NotFoundHttpException("Report with key [$slug] not found.");
        }

        $path = match ($extension) {
            'js' => $report->getSelectionControllerPath(),
            'xml' => $report->getSelectionViewPath(),
            default => throw new NotFoundHttpException("Unsupported file extension [$extension]."),
        };

        if (!is_file($path)) {
            throw new NotFoundHttpException("Report file [$path] not found.");
        }

        $mime = match ($extension) {
            'js' => 'application/javascript',
            'xml' => 'application/xml',
            default => 'text/plain',
        };

        return response(File::get($path), 200, [
            'Content-Type' => $mime,
        ]);
    }
}
