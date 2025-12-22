<?php

namespace LaravelUi5\Core\Middleware;

use Closure;
use Illuminate\Http\Request;
use LaravelUi5\Core\Contracts\Ui5ArtifactResolverInterface;
use LaravelUi5\Core\Contracts\Ui5ContextInterface;
use LaravelUi5\Core\Contracts\Ui5CoreContext;
use LaravelUi5\Core\Exceptions\MissingArtifactException;
use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;

/**
 * Middleware: ResolveUi5Context
 *
 * This middleware inspects incoming requests and attaches a Ui5Context
 * object to the service container if the request path belongs to a UI5 artifact.
 *
 * Responsibilities:
 * - Detect if the request path is under the configured UI5 route prefix.
 * - Derive the urlKey via ArtifactType::urlKeyFromPath().
 * - Lookup the matching artifact in the Ui5Registry.
 * - Build a Ui5Context (request, artifact, locale).
 *
 * Behavior:
 * - If no artifact is matched → no Ui5Context is bound, request proceeds normally.
 * - If an artifact is matched but not found in the registry → {@see MissingArtifactException}.
 *
 * The resolved Ui5Context can be injected anywhere downstream
 * via type-hinting or app(Ui5Context::class).
 */
class ResolveUi5Context
{
    public const string SESSION_KEY_PARTNER_ID = 'impersonate.partner_id';

    public function __construct()
    {
    }

    public function handle(Request $request, Closure $next)
    {
        /** @var Ui5ArtifactResolverInterface[] $resolvers */
        $resolvers = app('ui5.artifact.resolvers');

        foreach ($resolvers as $resolver) {
            $artifact = $resolver->resolve($request);

            if ($artifact) {
                $this->bindContext($request, $artifact);
                break;
            }
        }

        return $next($request);
    }

    protected function bindContext(Request $request, Ui5ArtifactInterface $artifact): void
    {
        $locale = $request->getLocale();

        app()->instance(Ui5ContextInterface::class, new Ui5CoreContext($request, $artifact, $locale));
    }
}
