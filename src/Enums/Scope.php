<?php

namespace LaravelUi5\Core\Enums;

/**
 * Defines the override hierarchy level at which a configurable value is stored.
 *
 * Scope represents the resolution layer of a Setting value.
 * It determines WHICH value wins when multiple overrides exist.
 *
 * Scope is not related to identity (SystemLevel)
 * and not related to governance (EditLevel).
 * It strictly models override precedence.
 *
 * Resolution follows a deterministic hierarchy:
 *
 *   Platform < Installation < Tenant < Site < User
 *
 * When resolving a Setting, the value with the highest available
 * Scope for the given key wins.
 *
 * Scope answers:
 *   "At which structural layer was this value defined?"
 *
 * It does NOT answer:
 *   - Who may edit it (EditLevel handles that)
 *   - Who the partner is (SystemLevel handles that)
 *   - What abilities are granted (SdkRole handles that)
 *
 * Scope is:
 * - persisted per setting entry,
 * - installation-wide and ordered,
 * - used exclusively for override resolution,
 * - compared numerically (higher value overrides lower value).
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
