<?php

namespace LaravelUi5\Core\Attributes;

use Attribute;

/**
 * Declares a semantic role within the system.
 *
 * A role represents a *conceptual responsibility or capability set*,
 * not a user group or access control rule. It is used to express
 * the intended purpose or function of users or modules on a semantic level.
 *
 * Example:
 * ```php
 * #[Role('Accountant', 'Responsible for financial postings and reporting.')]
 * class AccountantRole {}
 * ```
 *
 * Roles are typically referenced by `Ability` or `Group` assignments
 * but do not directly enforce access restrictions. They serve as
 * metadata to describe and organize responsibilities across modules.
 *
 * @see \LaravelUi5\Core\Attributes\Ability
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Role
{
    /**
     * Create a new role definition.
     *
     * @param string $title The display name of the role (e.g., "Accountant").
     * @param string $description Optional description of the role's purpose or scope.
     */
    public function __construct(
        public string $title,
        public string $description,
    ) {}
}
