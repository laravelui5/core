<?php

namespace LaravelUi5\Core\Enums;

/**
 * Defines symbolic visibility roles for settings.
 *
 * These roles determine the minimum permission level required to edit a setting.
 * Lower integer values indicate higher privileges.
 */
enum SettingVisibilityRole: int
{
    /**
     * Highest administrative role in the system.
     */
    case SuperAdmin = 1;

    /**
     * Mandate-level administrators (e.g., consultants or rollout coordinators).
     */
    case TenantAdmin = 2;

    /**
     * Local administrators within a tenant (e.g., site leads).
     */
    case SiteAdmin = 3;

    /**
     * Functional leads or team supervisors.
     */
    case Supervisor = 4;

    /**
     * Regular users with base-level permissions.
     */
    case Employee = 5;

    /**
     * Returns a human-readable label for the role.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::TenantAdmin => 'Tenant Admin',
            self::SiteAdmin => 'Site Admin',
            self::Supervisor => 'Supervisor',
            self::Employee => 'Employee',
        };
    }

    /**
     * Checks if the current role is sufficient to edit a setting
     * that requires a given visibility role.
     *
     * Example:
     * SiteAdmin->allows(Employee) => true
     * Supervisor->allows(SiteAdmin) => false
     *
     * @param SettingVisibilityRole $required
     * @return bool
     */
    public function allows(SettingVisibilityRole $required): bool
    {
        return $this->value <= $required->value;
    }
}
