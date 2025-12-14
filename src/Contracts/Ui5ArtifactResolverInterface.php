<?php

namespace LaravelUi5\Core\Contracts;

use Illuminate\Http\Request;
use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;

interface Ui5ArtifactResolverInterface
{
    /**
     * Attempt to resolve a Ui5Artifact from the given request.
     *
     * @param Request $request
     * @return Ui5ArtifactInterface|null
     */
    public function resolve(Request $request): ?Ui5ArtifactInterface;
}
