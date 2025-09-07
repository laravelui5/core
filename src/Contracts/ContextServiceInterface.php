<?php

namespace LaravelUi5\Core\Contracts;

use RuntimeException;

/**
 * Interface ContextServiceInterface
 *
 * Defines the contract for resolving and delivering the full context
 * required by UI5 applications at runtime.
 *
 * The resolved context typically includes settings, abilities, user info,
 * and other domain-specific fragments contributed by registered providers.
 *
 * The service supports two modes of use:
 * 1. In HTTP/UI5 environments, where the context is built and injected automatically via middleware
 * 2. In background jobs, tests, or CLI tools, where a context may need to be constructed manually
 */
interface ContextServiceInterface
{
    /**
     * Returns the fully assembled context array for the current request.
     *
     * This method assumes that a Ui5RuntimeContext has already been resolved
     * and is available via the service container.
     *
     * The returned structure is a key-value array, where each key maps to a
     * contributed context fragment (e.g. `settings`, `user`, `can`, etc.).
     *
     * @return array<string, mixed>
     *
     * @throws RuntimeException if the context is missing or invalid
     *
     * @see Ui5Context
     * @see ResolveUi5Context
     */
    public function getContext(): array;

    /**
     * Constructs a Ui5RuntimeContext from raw input parameters.
     *
     * This is typically used in non-HTTP scenarios like background jobs, tests,
     * or system-level batch processing.
     *
     * It does not resolve contributors or context fragments automatically —
     * you must pass the resulting context to getContext() or contributors manually.
     *
     * @param string $ui5AppId The technical ID of the UI5 App (e.g., "io.pragmatiqu.offers")
     * @param TenantInterface|null $tenant
     * @param BusinessPartnerInterface|null $partner The acting business partner, or null if anonymous/system
     * @param BusinessPartnerInterface|null $authPartner The authenticated (original) business partner, if different
     * @param string|null $locale The locale to use (overrides $tenant->getLocale())
     * @return Ui5Context
     */
    public function makeContext(
        string                    $ui5AppId,
        ?TenantInterface          $tenant = null,
        ?BusinessPartnerInterface $partner = null,
        ?BusinessPartnerInterface $authPartner = null,
        ?string                   $locale = null
    ): Ui5Context;
}
