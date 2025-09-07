<?php

namespace LaravelUi5\Core\Controllers;

use Illuminate\Http\JsonResponse;
use LaravelUi5\Core\Contracts\Ui5Context;
use LaravelUi5\Core\Services\ExecutableHandler;
use LaravelUi5\Core\Ui5\Contracts\Ui5ActionInterface;

/**
 * Controller responsible for dispatching UI5 Actions.
 *
 * Flow:
 * - Resolves the Action artifact from the Ui5Context/registry.
 * - Obtains the ActionHandler behind the Action.
 * - Injects parameters and settings if supported.
 * - Executes the handler and returns the result (JSON by default).
 *
 * Notes:
 * - Actions are state-changing operations (POST/PATCH/DELETE).
 * - Unlike Resources, they may not be idempotent.
 */
class ActionDispatchController
{
    public function __invoke(Ui5Context $context, ExecutableHandler $handler): JsonResponse
    {
        /** @var Ui5ActionInterface $action */
        $action = $context->artifact;

        $result = $handler->run($action->getHandler());

        return response()->json($result);
    }
}
