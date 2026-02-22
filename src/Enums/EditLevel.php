<?php

namespace LaravelUi5\Core\Enums;

/**
 * Defines the governance threshold required to modify or view a resource.
 *
 * EditLevel represents a policy requirement — not an identity.
 * It is used to declare the minimum structural authority needed
 * to edit (and optionally see) a Setting or other configurable artifact.
 *
 * EditLevel values are compared against a partner's SystemLevel
 * (mapped to EditLevel) to determine whether an operation is allowed.
 *
 * Conceptually:
 *   SystemLevel → WHO the partner is (identity)
 *   EditLevel   → WHICH authority level is required (policy)
 *
 * A partner may modify a resource if:
 *
 *     partner.system_level.toEditLevel() >= required EditLevel
 *
 * EditLevel is:
 * - defined in Core (because Settings are declared in Core),
 * - installation-wide and not module-scoped,
 * - numeric and ordered (higher value = higher authority),
 * - used purely for governance decisions,
 * - independent of functional RBAC roles (SdkRole).
 *
 * EditLevel does NOT define abilities or access rights directly.
 * It defines the structural threshold required to perform changes.
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
