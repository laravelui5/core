<?php

namespace LaravelUi5\Core\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use LaravelUi5\Core\Contracts\ExecutableInvokerInterface;
use LaravelUi5\Core\Contracts\Ui5ContextInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ReportInterface;

class ReportController
{
    public function __invoke(Ui5ContextInterface $context, ExecutableInvokerInterface $invoker): Factory|View|Application
    {
        /** @var Ui5ReportInterface $report */
        $report = $context->artifact();

        $data = $invoker->invoke(
            $report->getProvider(),
            'provide'
        );

        return view($report->getReportView(), $data);
    }
}
