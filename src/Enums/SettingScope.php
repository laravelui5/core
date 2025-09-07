<?php

namespace LaravelUi5\Core\Enums;

/**
 * Represents the applicability level (scope) of a setting.
 *
 * Resolution follows a strict precedence chain:
 * USER (4) > TEAM (3) > APP (2) > TENANT (1)
 *
 * Settings with a higher scope value override those with a lower value.
 */
enum SettingScope: int
{
    /**
     * The setting applies to the entire tenant (defaults).
     * Commonly provided by the vendor (e.g., Pragmatiqu IT).
     */
    case Tenant = 1;

    /**
     * The setting applies to a specific project or application configuration.
     * Typically set during rollout by a site_admin or tenant_admin.
     */
    case App = 2;

    /**
     * The setting applies to a team (a group of business partners).
     * Useful for group-wide display preferences or reports.
     */
    case Team = 3;

    /**
     * The setting applies to a single user (personalization).
     * This has the highest precedence.
     */
    case User = 4;

    /**
     * Returns the symbolic label of the scope, e.g., 'USER'.
     */
    public function label(): string
    {
        return match ($this) {
            self::Tenant => 'TENANT',
            self::App => 'APP',
            self::Team => 'TEAM',
            self::User => 'USER',
        };
    }

    /**
     * Indicates whether the current scope has higher precedence than another.
     *
     * @param SettingScope $other
     * @return bool
     */
    public function overrides(SettingScope $other): bool
    {
        return $this->value > $other->value;
    }
}
