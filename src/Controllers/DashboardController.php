<?php

namespace LaravelUi5\Core\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Blade;
use LaravelUi5\Core\Contracts\Ui5ContextInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5DashboardInterface;

class DashboardController
{
    public function __invoke(Ui5ContextInterface $context, string $slug): Response
    {
        /** @var Ui5DashboardInterface $dashboard */
        $dashboard = $context->artifact();

        $xml = Blade::render($dashboard->getDashboard());

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
