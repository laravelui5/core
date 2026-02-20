<?php

namespace LaravelUi5\Core\Enums;

/**
 * Defines symbolic visibility roles for settings.
 *
 * These roles determine the minimum permission level required to edit a setting.
 */
enum EditLevel: int
{
    /**
     * Highest administrative role in the system.
     */
    case SuperAdmin = 5;

    /**
     * Mandate-level administrators (e.g., consultants or rollout coordinators).
     */
    case TenantAdmin = 4;

    /**
     * Local administrators within a tenant (e.g., site leads).
     */
    case SiteAdmin = 3;

    /**
     * Functional leads or team supervisors.
     */
    case Supervisor = 2;

    /**
     * Regular users with base-level permissions.
     */
    case Employee = 1;

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
     * @param EditLevel $required
     * @return bool
     */
    public function allows(EditLevel $required): bool
    {
        return $this->value >= $required->value;
    }
}
