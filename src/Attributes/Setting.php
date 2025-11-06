<?php

namespace LaravelUi5\Core\Attributes;

use LaravelUi5\Core\Enums\SettingScope;
use LaravelUi5\Core\Enums\ValueType;
use LaravelUi5\Core\Enums\SettingVisibilityRole;

/**
 * Declarative setting definition for Configurable classes.
 *
 * Apply this attribute multiple times on a class (e.g., Action handler, Resource/Data provider,
 * Report provider) to declare the settings it depends on.
 *
 *  **When to Use Settings vs. Custom Database Models**
 *
 *  The *Settings API* is designed for lightweight, flexible configuration values that can be expressed as simple key–value pairs (strings, numbers, booleans, arrays, dates).
 *  It works well when the configuration is *UI-driven, ephemeral, or low-risk*:
 *
 *   - UI filters and personalization (e.g., a card showing weekly hours for selected employees).
 *   - Display preferences (e.g., default currency, theme, date range presets).
 *   - Feature toggles or thresholds that may change frequently.
 *   - Lists of IDs that serve as filters, *as long as you accept weak references* (IDs stored without DB-level foreign keys).
 *
 *  By contrast, a *custom database model* should be used when the configuration expresses a *business relationship or critical domain rule*:
 *
 *   - Relationships that must be referentially intact (e.g., employees assigned to cost centers).
 *   - Audit-relevant or legally binding data (e.g., who is eligible for billing, regulatory roles).
 *   - Data that requires strong constraints, migrations, or reporting queries (joins, aggregates).
 *   - Long-lived associations where zombie IDs (deleted or reassigned objects) are unacceptable.
 *
 *  **Rule of Thumb**
 *
 *   If a value is *ephemeral, UI-scoped, or best-effort* → *Settings* are pragmatic and safe.
 *   If a value is *domain-critical, audited, or relational in nature* → design a *dedicated DB model*.
 *
 *  **Notes**
 *
 *   Weak foreign keys in Settings (e.g., arrays of BusinessPartner IDs) are acceptable if you *sanitize them at runtime* (ignore IDs that no longer exist).
 *   Settings should always store JSON-safe primitives (string, int, float, bool, date in ISO-8601, arrays thereof).
 *   You trade off *referential integrity* for *developer speed*. This is intentional.
 *
 *  **Example**
 *
 *  ```#[Setting(
 *      key: 'billing.settlement.validation.maxHours',
 *      type: SettingValueType::Integer,
 *      default: 8,
 *      scope: SettingScope::Tenant,
 *      visibilityRole: SettingVisibilityRole::TenantAdmin
 *  )]```
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Setting
{
    /**
     * @param string $key Fully qualified dot key, e.g. "module.service.setting.foo.bar"
     * @param ValueType $type Type used to cast the JSON value (maps to `value_type`)
     * @param mixed $default Default applied by package (developer)
     * @param SettingScope $scope Intended/default scope for this setting (maps to `scope`)
     * @param SettingVisibilityRole $visibilityRole Minimum role allowed to edit (maps to `visibility_role`)
     */
    public function __construct(
        public string                $key,
        public ValueType             $type = ValueType::String,
        public mixed                 $default = null,
        public SettingScope          $scope = SettingScope::Tenant,
        public SettingVisibilityRole $visibilityRole = SettingVisibilityRole::TenantAdmin,
    )
    {
    }
}
