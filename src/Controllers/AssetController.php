<?php

namespace LaravelUi5\Core\Controllers;

use Illuminate\Routing\Controller;
use LaravelUi5\Core\Contracts\Ui5ContextInterface;
use LaravelUi5\Core\Exceptions\MissingAssetException;
use LaravelUi5\Core\Ui5\Capabilities\HasAssetsInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Delivers static assets for UI5 applications or libraries (e.g., JS, CSS, i18n).
 *
 * Assets are resolved via the Ui5Registry based on artifact slug and type.
 * The version segment is accepted in the URL for cache-busting but currently
 * ignored in path resolution.
 *
 * Example routes:
 * - GET /app/users/1.0.0/Component.js
 * - GET /lib/core/1.0.0/resources/i18n/i18n.properties
 */
class AssetController extends Controller
{
    public function __invoke(
        Ui5ContextInterface $context,
        string         $type,
        string         $slug,
        string         $version,
        string         $file
    ): BinaryFileResponse
    {

        $artifact = $context->artifact();

        if ($artifact instanceof HasAssetsInterface) {
            $path = $artifact->getAssetPath($file);

            if ($path && file_exists($path)) {
                $mime = match (true) {
                    str_ends_with($file, '.js') => 'application/javascript',
                    str_ends_with($file, '.js.map') => 'application/json',
                    str_ends_with($file, '.json') => 'application/json',
                    str_ends_with($file, '.css') => 'text/css',
                    str_ends_with($file, '.properties') => 'text/plain; charset=utf-8',
                    default => 'application/octet-stream',
                };

                return response()->file($path, ['Content-Type' => $mime]);
            }
        }

        throw new MissingAssetException($type, $slug, $version, $file);
    }
}
