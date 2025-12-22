<?php

namespace LaravelUi5\Core\Middleware;

use Closure;
use Illuminate\Http\Request;
use LaravelUi5\Core\Contracts\Ui5ContextInterface;
use LaravelUi5\Core\Exceptions\OutdatedVersionException;

/**
 * Ensures that only the latest version of a UI5 app artifact is accessible.
 *
 * This middleware checks whether the requested version of the UI5 application
 * (as provided in the route parameters) matches the currently registered version
 * in the resolved Ui5RuntimeContext. If the version does not match, a 410 Gone
 * response is returned to indicate that the requested artifact version is no longer available.
 *
 * Note: This middleware requires that the Ui5RuntimeContext has already been resolved
 * and registered in the service container. It should therefore be placed
 * **after** the `ResolveUi5RuntimeContext` middleware in the route or middleware stack.
 *
 * This middleware is **not applied by default** and should be explicitly included
 * for routes that require strict version enforcement.
 *
 * @package LaravelUi5\Core\Middleware
 */
class EnsureFrontendVersionIsLatest
{
    public function handle(Request $request, Closure $next)
    {
        /** @var Ui5ContextInterface|null $context */
        $context = app(Ui5ContextInterface::class);

        if ($context && $context->artifact() && $request->route('version')) {
            $requestedVersion = $request->route('version');
            $registeredVersion = $context->artifact()->getVersion();

            if ($requestedVersion !== $registeredVersion) {
                throw new OutdatedVersionException(
                    $context->artifact()->getNamespace(),
                    $requestedVersion,
                    $registeredVersion
                );
            }
        }

        return $next($request);
    }
}
