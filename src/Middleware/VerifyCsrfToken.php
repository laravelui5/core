<?php

namespace LaravelUi5\Core\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Exceptions\HttpResponseException;

class VerifyCsrfToken extends ValidateCsrfToken
{
    /**
     * From SAP documentation:
     *
     * > If a data request fails with status 403 and an "X-CSRF-Token" response header value "required" (case insensitive),
     * a new security token will be fetched and the data request will be repeated automatically and transparently.
     * >
     * > A new security token is fetched via a HEAD request on the service URL using an "X-CSRF-Token" header value "Fetch".
     * The response header value of "X-CSRF-Token" is remembered if present, or else that header will not be used any longer.
     *
     * @see https://sapui5.hana.ondemand.com/sdk/#/topic/9613f1f2d88747cab21896f7216afdac.html
     * @see FetchCsrfToken
     *
     * @param $request
     * @param Closure $next
     * @return \Illuminate\Support\HigherOrderTapProxy|mixed
     */
    public function handle($request, Closure $next): mixed
    {
        if (
            $this->isReading($request) ||
            $this->runningUnitTests() ||
            $this->inExceptArray($request) ||
            $this->tokensMatch($request)
        ) {
            return tap($next($request), function ($response) use ($request) {
                if ($this->shouldAddXsrfTokenCookie()) {
                    $this->addCookieToResponse($request, $response);
                }
            });
        }

        throw new HttpResponseException(
            response('CSRF token mismatch.')
                ->header('X-CSRF-Token', 'required')
                ->setStatusCode(403)
        );
    }
}
