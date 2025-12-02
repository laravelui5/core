<?php

namespace LaravelUi5\Core\Attributes;

use Attribute;
use LaravelUi5\Core\Enums\AbilityType;

/**
 * Declares an ability (permission concept) that a Ui5 artifact or module
 * exposes.  This is purely metadata discovered by reflection; Core does not
 * enforce any authorization.
 *
 * Each ability is defined by a unique name and a {@see AbilityType}.  The
 * resulting i18n keys for UI labels are composed automatically as:
 *
 *   • "<type>.<name>.title"
 *   • "<type>.<name>.description"
 *
 * Example:
 * ```php
 * use LaravelUi5\Core\Attributes\Ability;
 * use LaravelUi5\Core\Enums\AbilityType;
 *
 * #[Ability(
 *     name: 'exportContacts',
 *     role: 'Supervisor',
 *     type: AbilityType::Act,
 *     note: 'Allows exporting contact data to CSV or Excel.'
 * )]
 * class ExportContactsAction extends Ui5Action
 * {
 *     // ...
 * }
 * ```
 *
 * ### Reflection & SDK usage
 * During cache build the SDK records:
 *   - ability name
 *   - ability role
 *   - ability type (enum label or int)
 *   - optional developer note
 *
 * The SDK or higher layers may later map these abilities to Laravel
 * authorization gates or policy methods and resolve the derived i18n keys
 * for display.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Ability
{
    /**
     * @param string $ability Technical identifier, e.g. "exportContacts".
     * @param string $role $role  semantic bag for the ability
     * @param AbilityType $type Ability classification (Use, Act, See).
     * @param string $note Description of the ability's purpose or scope.
     */
    public function __construct(
        public string      $ability,
        public string      $role,
        public AbilityType $type = AbilityType::Act,
        public string      $note,
    ) {}
}
