<?php

namespace LaravelUi5\Core\Ui5\Contracts;

/**
 * Marker interface for UI5 artefacts that expose static assets.
 *
 * This allows Laravel controllers to retrieve the appropriate file path for resources
 * like preload scripts, i18n bundles, and maps, based on a given relative filename.
 */
interface HasAssetsInterface
{
    /**
     * Resolve the absolute path to an exposed asset (e.g., Component-preload.js).
     *
     * If the file exists, return the full path. Otherwise, return null.
     *
     * @param string $filename The relative asset filename requested
     * @return string|null The full filesystem path if it exists, or null otherwise
     */
    public function getAssetPath(string $filename): ?string;
}
