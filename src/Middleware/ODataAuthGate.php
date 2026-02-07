<?php

namespace LaravelUi5\Core\Middleware;

use Closure;
use Flat3\Lodata\Endpoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppInterface;
use Symfony\Component\HttpFoundation\Response;

class ODataAuthGate
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // opt-out for development
        if (!config('ui5.force_auth', true)) {
            return $next($request);
        }

        /** @var Ui5AppInterface $context */
        $context = app(Endpoint::class);

        $module = $context->getModule();

        // enforce authentication
        if ($module->requiresAuth()) {
            if (!Auth::check()) {
                return redirect()->guest(route('login'));
            }
        }

        return $next($request);
    }
}
