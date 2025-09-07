<?php

namespace LaravelUi5\Core\Contracts;

use Illuminate\Http\Request;

/**
 * Interface TenantResolverInterface
 *
 * Defines the mechanism for resolving a Tenant instance.
 *
 * This abstraction decouples Core from any concrete Tenant model or storage mechanism.
 * Applications can provide their own resolver implementation and register it
 * in the ui5.php configuration.
 *
 * Configuration example (`config/ui5.php`):
 *
 * 'tenant_resolver' => \App\Resolvers\MyTenantResolver::class,
 *
 * A default fallback resolver is provided in Core that always returns `null`.
 */
interface TenantResolverInterface
{
    /**
     * Resolve a Tenant instance.
     *
     * @param Request $request
     * @return TenantInterface|null
     */
    public function resolve(Request $request): ?TenantInterface;
}
