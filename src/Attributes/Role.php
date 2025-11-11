<?php

namespace LaravelUi5\Core\Attributes;

use Attribute;

/**
 * Declares a semantic role within the system.
 *
 * A Role groups Abilities into a *conceptual responsibility*,
 * such as "Accountant" or "Project Manager". Roles are global
 * and context-free. They describe *what* a user represents,
 * not *where* the role applies.
 *
 * Contextual assignments (e.g. "Anna is Project Manager in Project A")
 * are handled at the group or policy layer, not in the role itself.
 *
 * Example:
 * ```php
 * #[Role('Accountant', 'Responsible for financial postings and reporting.')]
 * class AccountingModule {}
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
     * @param string $role Technical identifier, e.g. "Accountant"), unique per installation.
     * @param string $note Description of the role's purpose or scope.
     */
    public function __construct(
        public string $role,
        public string $note,
    ) {}
}
