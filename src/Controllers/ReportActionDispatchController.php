<?php

namespace LaravelUi5\Core\Controllers;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\CircularDependencyException;
use Illuminate\Http\JsonResponse;
use LaravelUi5\Core\Contracts\Ui5ContextInterface;
use LaravelUi5\Core\Exceptions\InvalidReportActionException;
use LaravelUi5\Core\Exceptions\MissingReportActionException;
use LaravelUi5\Core\Services\ExecutableHandler;
use LaravelUi5\Core\Ui5\Capabilities\ReportActionInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ReportInterface;

class ReportActionDispatchController
{
    /**
     * @throws CircularDependencyException
     * @throws BindingResolutionException
     */
    public function __invoke(
        Ui5ContextInterface    $context,
        ExecutableHandler $handler,
        string            $slug,
        string            $action
    ): JsonResponse
    {
        /** @var Ui5ReportInterface $report */
        $report = $context->artifact();

        $actions = $report->getActions();
        if(!array_key_exists($action, $actions)) {
            throw new MissingReportActionException($action, $report->getTitle());
        }

        $instance = app($actions[$action]);

        if (!$instance instanceof ReportActionInterface) {
            throw new InvalidReportActionException($action);
        }

        try {
            $result = $handler->run($instance);

            return response()->json([
                'status' => 'Success',
                'code' => 200,
                'data' => $result,
            ]);
        }
        catch (Exception $e) {
            return response()->json([
                'status' => 'Error',
                'code' => 500,
                'message' => $e->getMessage(),
                'errors' => method_exists($e, 'errors') ? $e->errors() : null,
            ]);
        }
    }
}
