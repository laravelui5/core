<?php

namespace LaravelUi5\Core\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use LaravelUi5\Core\Contracts\Ui5CoreContext;
use LaravelUi5\Core\Exceptions\MissingCardManifestException;
use LaravelUi5\Core\Services\ExecutableHandler;
use LaravelUi5\Core\Ui5\Contracts\ConfigurableInterface;
use LaravelUi5\Core\Ui5\Contracts\ParameterizableInterface;
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
 * - If the provider implements {@see ParameterizableInterface},
 *   inject validated request parameters.
 * - If the provider implements {@see ConfigurableInterface},
 *   inject resolved settings.
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
    public function __invoke(
        Ui5CoreContext    $context,
        ExecutableHandler $dataProviderHandler,
        string            $app,
        string            $slug,
        string            $version
    ): Response
    {
        /** @var Ui5CardInterface $card */
        $card = $context->artifact;

        $manifestPath = base_path("ui5/{$app}/resources/ui5/cards/{$slug}.blade.php");
        if (!File::exists($manifestPath)) {
            throw new MissingCardManifestException($manifestPath);
        }

        $data = $dataProviderHandler->run($card->getProvider());

        $compiled = Blade::render(File::get($manifestPath), [
            'data' => $data,
        ]);

        return response($compiled, 200)->header('Content-Type', 'application/json');
    }
}
