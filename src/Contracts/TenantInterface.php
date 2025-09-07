<?php

namespace LaravelUi5\Core\Contracts;

/**
 * Lightweight tenant descriptor available at bootstrap time.
 *
 * Intent:
 *  - Immutable, DB-free reference to the current tenant.
 *  - Generated per-tenant PHP class (e.g., in ./app/Tenants/ACME.php).
 *  - Provides a stable ID, a display name, a locale, and an absolute asset root.
 *
 * Notes:
 *  - Keep implementations slim (no I/O, no secrets, no lazy-loading).
 *  - `getAssetPath()` should return an absolute filesystem path to the
 *    tenant’s asset root (logos, templates, CI files), e.g.:
 *      return __DIR__ . '/assets';
 *  - Do NOT return web URLs here. Build URLs via your delivery layer
 *    (e.g., a controller/Filesystem disk or a small TenantAssets helper).
 */
interface TenantInterface
{
    /**
     * Stable primary key for logs, cache keys, and settings scope.
     * Can be numeric or string (e.g., 'acme').
     *
     * @return int|string
     */
    public function getId(): int|string;

    /**
     * Human-readable tenant name (display or legal name).
     *
     * @return string
     */
    public function getName(): string;

    /**
     * The default locale for the tenant.
     *
     * @return string
     */
    public function getLocale(): string;

    /**
     * Absolute filesystem path to the tenant’s asset root (CI/templates).
     * Example implementation: `return __DIR__ . '/../assets';`
     *
     * @return string
     */
    public function getAssetPath(): string;
}
