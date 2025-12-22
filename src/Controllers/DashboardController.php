<?php

namespace LaravelUi5\Core\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;
use LaravelUi5\Core\Contracts\Ui5ContextInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5DashboardInterface;

class DashboardController
{
    public function __invoke(Ui5ContextInterface $context, string $slug): Response
    {
        /** @var Ui5DashboardInterface $dashboard */
        $dashboard = $context->artifact();

        $xml = View::file($dashboard->getDashboard())->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
