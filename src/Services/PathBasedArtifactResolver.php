<?php

namespace LaravelUi5\Core\Services;

use Illuminate\Http\Request;
use LaravelUi5\Core\Contracts\Ui5ArtifactResolverInterface;
use LaravelUi5\Core\Enums\ArtifactType;
use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5RegistryInterface;
use LaravelUi5\Core\Ui5CoreServiceProvider;

readonly class PathBasedArtifactResolver implements Ui5ArtifactResolverInterface
{

    public function __construct(
        private Ui5RegistryInterface $registry
    )
    {
    }

    public function resolve(Request $request): ?Ui5ArtifactInterface
    {
        $path = $request->path();

        if (!str_starts_with($path, Ui5CoreServiceProvider::UI5_ROUTE_PREFIX)) {
            return null;
        }
        $relative = trim(substr($path, strlen(Ui5CoreServiceProvider::UI5_ROUTE_PREFIX)), '/');

        $urlKey = ArtifactType::urlKeyFromPath($relative);

        if (is_null($urlKey)) {
            return null;
        }

        return $this->registry->fromSlug($urlKey);
    }
}
