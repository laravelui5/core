<?php

namespace LaravelUi5\Core\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use LaravelUi5\Core\Contracts\Ui5Context;
use LaravelUi5\Core\Services\ExecutableHandler;
use LaravelUi5\Core\Ui5\Contracts\Ui5ResourceInterface;

/**
 * Controller for executing UI5 Resources.
 *
 * This controller is invoked only for routes that map to UI5 Resources.
 * The current Ui5Context is provided by middleware and guarantees that
 * `$context->artifact` is an instance of Ui5ResourceInterface.
 *
 * Responsibilities:
 * - Resolve the ResourceDataProvider from the Resource artifact.
 * - If the provider implements ParameterizableInterface, inject validated parameters.
 * - If the provider implements ConfigurableInterface, inject resolved settings.
 * - Execute the provider and return the result as JSON.
 *
 * Notes:
 * - We assume the artifact in the context is always a Ui5ResourceInterface;
 *   no additional instanceof check is performed here.
 * - Execution is side-effect free: Resources are read-only endpoints (GET).
 */
class ResourceController extends Controller
{
    public function __invoke(Ui5Context $context, ExecutableHandler $dataProviderHandler): JsonResponse
    {
        /** @var Ui5ResourceInterface $resource */
        $resource = $context->artifact;

        $provider = $resource->getProvider();

        $result = $dataProviderHandler->run($provider);

        return response()->json($result);
    }
}
