<?php

namespace LaravelUi5\Core\Services;

use Illuminate\Http\Request;
use LaravelUi5\Core\Contracts\TenantInterface;
use LaravelUi5\Core\Contracts\TenantResolverInterface;

class NullTenantResolver implements TenantResolverInterface
{
    public function resolve(Request $request): ?TenantInterface
    {
        return null;
    }
}
