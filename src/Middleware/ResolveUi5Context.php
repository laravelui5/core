<?php

namespace LaravelUi5\Core\Middleware;

use Closure;
use Illuminate\Http\Request;
use LaravelUi5\Core\Contracts\Ui5ArtifactResolverInterface;
use LaravelUi5\Core\Contracts\Ui5ContextFactoryInterface;
use LaravelUi5\Core\Contracts\Ui5ContextInterface;
use LaravelUi5\Core\Exceptions\MissingArtifactException;
use Symfony\Component\HttpFoundation\Response;

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
 * - If no artifact is matched → {@see MissingArtifactException}.
 * - If an artifact is matched but not found in the registry → {@see MissingArtifactException}.
 *
 * The resolved Ui5Context can be injected anywhere downstream
 * via type-hinting or app(Ui5Context::class).
 */
readonly class ResolveUi5Context
{
    public function __construct(
        private Ui5ContextFactoryInterface $factory,
    )
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Ui5ArtifactResolverInterface[] $resolvers */
        $resolvers = app('ui5.artifact.resolvers');

        $artifact = null;
        foreach ($resolvers as $resolver) {
            $artifact = $resolver->resolve($request);
            if ($artifact) {
                break;
            }
        }

        if (!$artifact) {
            throw new MissingArtifactException($request->getRequestUri());
        }

        $context = $this->factory->build($request, $artifact);

        app()->instance(Ui5ContextInterface::class, $context);

        return $next($request);
    }
}
