<?php

namespace LaravelUi5\Core\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LaravelUi5\Core\Contracts\Ui5ContextInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;
use Symfony\Component\HttpFoundation\Response;

class Ui5AuthGate
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

        $context = app(Ui5ContextInterface::class);

        /** @var Ui5ModuleInterface $module */
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
