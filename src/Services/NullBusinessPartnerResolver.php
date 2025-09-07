<?php

namespace LaravelUi5\Core\Services;

use LaravelUi5\Core\Contracts\BusinessPartnerInterface;
use LaravelUi5\Core\Contracts\BusinessPartnerResolverInterface;

/**
 * Dummy resolver that always returns null.
 *
 * This is the default fallback resolver used in Core
 * if no custom resolver is configured in the application.
 */
class NullBusinessPartnerResolver implements BusinessPartnerResolverInterface
{
    public function resolveById(int $id): ?BusinessPartnerInterface
    {
        return null;
    }
}
