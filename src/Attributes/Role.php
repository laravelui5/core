<?php

namespace LaravelUi5\Core\Attributes;

use Attribute;

/**
 * Declares a semantic role within the LaravelUi5 domain model.
 *
 * A Role groups Abilities into a *conceptual responsibility*,
 * such as "Accountant" or "Project Manager". Roles are global
 * and context-independent — they describe *what* a user represents,
 * not *where* the role applies.
 *
 * Contextual assignments (e.g. "Anna is Project Manager in Project A")
 * are handled at the group or policy layer, not by the role itself.
 *
 * Example:
 * ```php
 * #[Role('Accountant', 'Responsible for financial postings and reporting.')]
 * class AccountingModule {}
 * ```
 *
 * Roles are typically referenced by `Ability` or `Group` assignments
 * but do not directly enforce access restrictions. They serve as
 * descriptive metadata to organize responsibilities across modules.
 *
 * @see \LaravelUi5\Core\Attributes\Ability
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Role
{
    /**
     * Create a new role definition.
     *
     * @param string      $role  Technical identifier (e.g. "Accountant"), unique per installation.
     * @param string      $note  Human-readable description of the role’s purpose or scope.
     * @param string|null $scope Optional fully-qualified class name that defines
     *                           the *semantic domain scope* of this role,
     *                           e.g. `Pragmatiqu\Projects\Models\Project`
     *                           for the role "Project Manager".
     */
    public function __construct(
        public string $role,
        public string $note,
        public ?string $scope = null,
    ) {}
}
