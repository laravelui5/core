<?php

namespace LaravelUi5\Core\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LaravelUi5\Core\Contracts\BusinessPartnerInterface;
use LaravelUi5\Core\Contracts\BusinessPartnerResolverInterface;
use LaravelUi5\Core\Contracts\HasBusinessPartnerInterface;
use LaravelUi5\Core\Contracts\TenantResolverInterface;
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
        if ($artifact = $this->resolveArtifact($request)) {

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

        return $next($request);
    }

    /**
     * Resolve the Ui5Artifact from the request path.
     *
     * This extracts the relative path (below the UI5 route prefix),
     * normalizes it into a urlKey, and queries the Ui5Registry.
     *
     * @param Request $request Current HTTP request
     * @return Ui5ArtifactInterface|null Returns the artifact if found,
     *                                   null if the request path is outside UI5 scope.
     *
     * @throws MissingArtifactException
     *         If the path looks like a UI5 artifact but no artifact is registered.
     */
    protected function resolveArtifact(Request $request): Ui5ArtifactInterface|null
    {
        $path = $request->path();

        if (str_starts_with($path, Ui5CoreServiceProvider::UI5_ROUTE_PREFIX)) {
            $relative = trim(substr($path, strlen(Ui5CoreServiceProvider::UI5_ROUTE_PREFIX)), '/');
            $urlKey = ArtifactType::urlKeyFromPath($relative);
            if ($artifact = $this->runtime->fromSlug($urlKey)) {
                return $artifact;
            }
            throw new MissingArtifactException($urlKey);
        }

        return null;
    }
}
