<?php

namespace LaravelUi5\Core\Contracts;

/**
 * Contract for models that are associated with a BusinessPartner.
 *
 * This interface provides a standardized way for the framework to access
 * the `BusinessPartnerInterface` related to an authenticated user or
 * another domain entity.
 *
 * Use Cases:
 * - Resolving the acting BusinessPartner from Auth::user().
 * - Supporting impersonation (e.g. session-stored partner IDs).
 * - Enforcing a consistent link between User models and BusinessPartner.
 *
 * Implementing Classes:
 * - Typically applied to your User model (`App\Models\User`).
 * - Can also be implemented by other models that carry a partner context.
 *
 * Example:
 * <code>
 * class User extends Authenticatable implements HasBusinessPartnerInterface
 * {
 *     public function partner(): ?BusinessPartnerInterface
 *     {
 *         return $this->belongsTo(BusinessPartner::class, 'partner_id')->first();
 *     }
 * }
 * </code>
 *
 * @see BusinessPartnerInterface
 */
interface HasBusinessPartnerInterface
{
    /**
     * Returns the BusinessPartner instance associated with this entity,
     * or null if none is assigned.
     *
     * @return BusinessPartnerInterface|null
     */
    public function partner(): ?BusinessPartnerInterface;
}
