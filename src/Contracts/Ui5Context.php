<?php

namespace LaravelUi5\Core\Contracts;

use Illuminate\Http\Request;
use LaravelUi5\Core\Middleware\ResolveUi5Context;
use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;

/**
 * Represents the runtime context of a UI5 artifact request.
 *
 * This context encapsulates all scoped information (tenant, partner, artifact,
 *  request, locale) required to evaluate settings, resolve permissions, and
 *  execute providers consistently.
 *
 * **Scope**
 *  - *HTTP requests*: Constructed by middleware from the current Request/Route.
 *  - *Console/Queue/Test*: Can be created manually without a Request
 *    (Request is nullable). Useful for jobs, batch reports, migrations, or tests.
 *
 * **Guidelines**
 *  - Do not directly depend on $request being non-null in providers.
 *  - Always access tenant/partner/artifact/locale via this context, not via globals.
 *  - Treat as immutable: one Ui5Context per logical execution.
 *
 * Access via dependency injection or the service container:
 *
 * <code>
 * $context = app(Ui5Context::class);
 * </code>
 *
 * @see ResolveUi5Context
 */
final readonly class Ui5Context
{
    /**
     * @param Request|null $request The current HTTP request (null if running in console)
     * @param Ui5ArtifactInterface $artifact The current UI5 artifact (App, Card, Report)
     * @param TenantInterface|null $tenant The tenant the app is running in (if applicable)
     * @param BusinessPartnerInterface|null $partner The acting business partner (can be impersonated)
     * @param BusinessPartnerInterface|null $authPartner The originally authenticated partner (if different)
     * @param string|null $locale Effective locale (overrides tenant->getLocale() if provided)
     */
    public function __construct(
        public ?Request $request = null,
        public Ui5ArtifactInterface $artifact,
        public ?TenantInterface $tenant = null,
        public ?BusinessPartnerInterface $partner = null,
        public ?BusinessPartnerInterface $authPartner = null,
        public ?string $locale = null,
    )
    {
    }

    /**
     * Effective locale, falling back to tenant->getLocale() if not explicitly set.
     *
     * @return string|null
     */
    public function effectiveLocale(): ?string
    {
        return $this->locale ?? $this->tenant?->getLocale();
    }
}
