<?php

namespace LaravelUi5\Core\Contracts;

/**
 * Interface BusinessPartnerResolverInterface
 *
 * This interface defines the mechanism for resolving a BusinessPartner instance
 * by its primary key (e.g., for impersonation, runtime context construction, etc.).
 *
 * It is designed to decouple the core framework from any specific BusinessPartner model
 * or ORM implementation.
 *
 * Packages that implement BusinessPartner functionality (e.g. a User or Employee model)
 * should register their resolver in the application configuration.
 *
 * Configuration example (`config/ui5.php`):
 *
 * 'business_partner_resolver' => \App\Resolvers\MyBusinessPartnerResolver::class,
 *
 * A default fallback resolver is provided in Core that always returns `null`.
 */
interface BusinessPartnerResolverInterface
{
    /**
     * Resolve a BusinessPartner instance by its ID.
     *
     * @param int $id
     * @return BusinessPartnerInterface|null
     */
    public function resolveById(int $id): ?BusinessPartnerInterface;
}
