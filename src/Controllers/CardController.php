<?php

namespace LaravelUi5\Core\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Blade;
use LaravelUi5\Core\Contracts\ExecutableInvokerInterface;
use LaravelUi5\Core\Contracts\Ui5ContextInterface;
use LaravelUi5\Core\Ui5\Capabilities\DataProviderInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5CardInterface;

/**
 * Controller for serving UI5 Card manifests with embedded data.
 *
 * This controller is invoked only for routes that resolve to UI5 Cards.
 * The Ui5Context middleware guarantees that `$context->artifact` is
 * an instance of {@see Ui5CardInterface}.
 *
 * Responsibilities:
 * - Locate the Blade-based card manifest by convention
 *   (`ui5/{app}/resources/ui5/cards/{slug}.blade.php`).
 * - Resolve the associated {@see DataProviderInterface}.
 * - Execute the provider and render the manifest with the resulting data.
 * - Return the compiled manifest as JSON to the client.
 *
 * Notes:
 * - Cards are read-only artifacts; providers must not mutate application state.
 * - The manifest Blade file is expected to output valid JSON.
 * - Missing manifests result in HTTP 404; execution always returns 200,
 *   even for empty data arrays.
 */
class CardController extends Controller
{
    public function __invoke(Ui5ContextInterface $context, ExecutableInvokerInterface $invoker): Response
    {
        /** @var Ui5CardInterface $card */
        $card = $context->artifact();

        $data = $invoker->invoke(
            $card->getProvider(),
            'provide'
        );

        $compiled = Blade::render($card->getManifest(), [
            'card' => $card,
            'data' => $data,
        ]);

        return response($compiled, 200)->header('Content-Type', 'application/json');
    }
}
