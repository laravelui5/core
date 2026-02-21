<?php

namespace LaravelUi5\Core\Enums;

/**
 * Represents the applicability level (scope) of a setting.
 *
 * Resolution follows a strict precedence chain:
 * USER (4) > SITE (3) > TENANT (2) > INSTALLATION (1) > PLATFORM (0)
 *
 * Settings with a higher scope value override those with a lower value.
 */
enum Scope: int
{
    case Platform = 0;
    case Installation = 1;
    case Tenant = 2;
    case Site = 3;
    case User = 4;

    /**
     * Returns the symbolic label of the scope, e.g., 'USER'.
     */
    public function label(): string
    {
        return match ($this) {
            self::Platform     => 'Platform',
            self::Installation => 'Installation',
            self::Tenant       => 'Tenant',
            self::Site         => 'Site',
            self::User         => 'User',
        };
    }

    /**
     * Indicates whether the current scope has higher precedence than another.
     *
     * @param Scope $other
     * @return bool
     */
    public function overrides(Scope $other): bool
    {
        return $this->value > $other->value;
    }
}
