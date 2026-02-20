<?php

namespace LaravelUi5\Core\Enums;

/**
 * Represents the applicability level (scope) of a setting.
 *
 * Resolution follows a strict precedence chain:
 * USER (3) > APP (2) > TENANT (1) > INSTALLTION (0)
 *
 * Settings with a higher scope value override those with a lower value.
 */
enum ReadScope: int
{
    /**
     * The base or installation-wide default.
     * Defined by the developer via attributes or config files.
     * Cannot be changed at runtime.
     */
    case Installation = 0;

    /**
     * The setting applies to the entire tenant (defaults).
     * Commonly provided by the vendor (e.g., Pragmatiqu IT).
     */
    case Tenant = 1;

    /**
     * The setting applies to a specific project or application configuration.
     * Typically set during rollout by a site_admin or tenant_admin.
     */
    case Artifact = 2;

    /**
     * The setting applies to a single user (personalization).
     * This has the highest precedence.
     */
    case User = 3;

    /**
     * Returns the symbolic label of the scope, e.g., 'USER'.
     */
    public function label(): string
    {
        return match ($this) {
            self::Installation => 'Installation',
            self::Tenant       => 'Tenant',
            self::Artifact     => 'Artifact',
            self::User         => 'User',
        };
    }

    /**
     * Indicates whether the current scope has higher precedence than another.
     *
     * @param ReadScope $other
     * @return bool
     */
    public function overrides(ReadScope $other): bool
    {
        return $this->value > $other->value;
    }
}
