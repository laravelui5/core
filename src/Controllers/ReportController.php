<?php

namespace LaravelUi5\Core\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use LaravelUi5\Core\Contracts\Ui5Context;
use LaravelUi5\Core\Services\ExecutableHandler;
use LaravelUi5\Core\Ui5\Contracts\Ui5ReportInterface;

class ReportController
{
    public function __invoke(
        Request           $request,
        Ui5Context        $context,
        ExecutableHandler $handler,
        string            $slug
    ): Factory|View|Application
    {
        /** @var Ui5ReportInterface $report */
        $report = $context->artifact;

        $provider = $report->getProvider();

        $data = $handler->run($provider);

        return view($report->getReportView(), $data);
    }
}
