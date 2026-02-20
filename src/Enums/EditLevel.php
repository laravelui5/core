<?php

namespace LaravelUi5\Core\Enums;

/**
 * Defines symbolic visibility roles for settings.
 *
 * These roles determine the minimum permission level required to edit a setting.
 */
enum EditLevel: int
{
    case User = 1;
    case Administrator = 2;
    case Organization = 3;
    case Operator = 4;
    case Platform = 5;

    /**
     * Returns a human-readable label for the role.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::Platform      => 'Platform',
            self::Operator      => 'Operator',
            self::Organization  => 'Organization',
            self::Administrator => 'Administrator',
            self::User      => 'User',
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
