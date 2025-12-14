<?php

namespace LaravelUi5\Core\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LaravelUi5\Core\Contracts\BusinessPartnerInterface;
use LaravelUi5\Core\Contracts\BusinessPartnerResolverInterface;
use LaravelUi5\Core\Contracts\HasBusinessPartnerInterface;
use LaravelUi5\Core\Contracts\TenantResolverInterface;
use LaravelUi5\Core\Contracts\Ui5ArtifactResolverInterface;
use LaravelUi5\Core\Contracts\Ui5Context;
use LaravelUi5\Core\Enums\ArtifactType;
use LaravelUi5\Core\Exceptions\MissingArtifactException;
use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5RuntimeInterface;
use LaravelUi5\Core\Ui5CoreServiceProvider;

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
 * - Resolve tenant and business partner (including impersonation).
 * - Build a Ui5Context (artifact, tenant, partner, authPartner, locale).
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

    public function __construct(
        protected Ui5RuntimeInterface $runtime,
    )
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
        /** @var TenantResolverInterface $tenantResolver */
        $tenantResolver = app(TenantResolverInterface::class);
        $tenant = $tenantResolver->resolve($request);

        $authUser = Auth::user();

        $authPartner = $authUser instanceof HasBusinessPartnerInterface
            ? $authUser->partner()
            : null;

        $impersonatePartnerId = session(self::SESSION_KEY_PARTNER_ID);

        $partnerResolver = app(BusinessPartnerResolverInterface::class);

        /** @var BusinessPartnerInterface $partner */
        $partner = $impersonatePartnerId
            ? $partnerResolver->resolveById($impersonatePartnerId)
            : $authPartner;

        $locale = $request->getLocale();

        app()->instance(Ui5Context::class, new Ui5Context(
            request: $request,
            artifact: $artifact,
            tenant: $tenant,
            partner: $partner,
            authPartner: $authPartner,
            locale: $locale
        ));
    }
}
