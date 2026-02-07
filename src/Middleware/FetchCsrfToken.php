<?php

namespace LaravelUi5\Core\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * From SAP documentation: **Security Token Handling**
 *
 * > The OData V4 model automatically handles a security token via an "X-CSRF-Token"
 * header if needed by its service. To achieve this, the "X-CSRF-Token" header starts
 * with a value of "Fetch" and will be included in every data request. If a data
 * response contains the "X-CSRF-Token" header, that new value will be remembered
 * and used from that time on.
 *
 * This middleware ensures that when a request asks for a CSRF token
 * (`X-CSRF-Token: Fetch`), the response will include the current CSRF token
 * in the `X-CSRF-Token` header.
 *
 * @see https://sapui5.hana.ondemand.com/sdk/#/topic/9613f1f2d88747cab21896f7216afdac.html
 * @see VerifyCsrfToken
 */
class FetchCsrfToken
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ('Fetch' === $request->header('X-CSRF-Token')) {
            $token = csrf_token();

            $response->headers->set('X-CSRF-Token', $token);
        }

        return $response;
    }
}
