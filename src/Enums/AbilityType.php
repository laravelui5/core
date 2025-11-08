<?php

namespace LaravelUi5\Core\Enums;

use InvalidArgumentException;

/**
 * Defines the four fundamental categories of Abilities within LaravelUi5.
 * Each type expresses a distinct semantic permission scope that governs
 * what a user may see, do, or enter across frontend and backend layers.
 *
 * Integer-backed for compact storage and stable comparison.
 *
 * ---------------------------------------------------------------------
 * Use    → Access to frontend views, dialogs, or routes
 * Act    → Execution of backend operations or actions
 * See    → Visibility control for UI elements
 * Access → Permission to enter system-level artifacts (apps, dashboards,
 *             reports, cards, tiles, KPIs, dialogs, resources)
 * ---------------------------------------------------------------------
 *
 * @example
 * if ($ability->type === AbilityType::Act) {
 *     // Perform business logic for action-level permission
 * }
 */
enum AbilityType: int
{
    /**
     * Frontend-level access to views, dialogs, or routes declared in manifest.json
     */
    case Use = 0;

    /**
     * Permission to execute a backend operation or service action.
     *
     * Implementors that want to secure a frontend operation should
     * work via the `AbilityType::See` instead.
     */
    case Act = 1;

    /**
     * Controls visibility of UI elements such as buttons, dialogs,
     * or sections. Typically used in the frontend to bind `visible`
     * or `enabled` states to user abilities.
     */
    case See = 2;

    /**
     * Backend-level access to entry-point artifacts without manifest anchors
     */
    case Access = 3;

    /**
     * Returns the canonical lowercase label used in manifests and caches.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::Use => 'use',
            self::Act => 'act',
            self::See => 'see',
            self::Access => 'access',
        };
    }

    /**
     * Creates an AbilityType from its canonical lowercase label.
     *
     * @param string $label
     * @return self
     */
    public static function fromLabel(string $label): self
    {
        return match ($label) {
            'use' => self::Use,
            'act' => self::Act,
            'see' => self::See,
            'access' => self::Access,
            default => throw new InvalidArgumentException("Unknown AbilityType label: $label"),
        };
    }

    /**
     * Returns true if this AbilityType represents an action-level permission.
     *
     * @return bool
     */
    public function isAct(): bool
    {
        return $this === self::Act;
    }

    /**
     * Returns true if this AbilityType represents an access-level permission.
     *
     * @return bool
     */
    public function isAccess(): bool
    {
        return $this === self::Access;
    }

    /**
     * Determines whether this AbilityType should be declared
     * inside the manifest.json (frontend domain) rather than
     * in backend PHP annotations.
     */
    public function shouldBeInManifest(): bool
    {
        return match ($this) {
            self::Use, self::See => true,
            default => false,
        };
    }
}
