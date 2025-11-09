<?php

namespace LaravelUi5\Core\Attributes;

use Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * Declares the primary *semantic object* represented by a Ui5Module.
 *
 * A semantic object is the logical entry point of a module — describing which
 * business entity this module "owns" (e.g. BusinessPartner, Project, Invoice)
 * and how other modules or the UI can reach it via named navigation intents.
 *
 * Each Ui5Module must declare **exactly one** SemanticObject.
 *
 * ### Purpose
 * This attribute enables:
 *  - automatic cross-module linking via {@see SemanticLink}
 *  - semantic intent navigation in the UI
 *  - backend reflection and registry consistency
 *
 * ### Example
 * ```php
 * use LaravelUi5\Core\Attributes\SemanticObject;
 *
 * #[SemanticObject(
 *     model: \Pragmatiqu\Partners\Models\Partner::class,
 *     name:  'BusinessPartner',
 *     routes: [
 *         'detail' => [
 *             'uri'   => '/Detail/{id}',
 *             'label' => 'Partner Details',
 *             'icon'  => 'sap-icon://person-placeholder',
 *         ],
 *         'roles' => [
 *             'uri'   => '/Roles?partner_id={id}',
 *             'label' => 'Assigned Roles',
 *             'icon'  => 'sap-icon://role',
 *         ],
 *     ],
 *     icon: 'sap-icon://group'
 * )]
 * class PartnersModule extends Ui5Module {}
 * ```
 *
 * ### Parameters
 * @param string $model Fully qualified Eloquent model class that represents
 *                        the business entity. Must exist and be autoloadable.
 * @param string $name Canonical semantic name used for system integration.
 *                        English only, PascalCase, unique system-wide.
 * @param array $routes Named navigation intents. Each must define at least:
 *                        - `uri`   (string URI template, may contain {id})
 *                        - `label` (human label for the UI)
 *                        Optional: `icon` (UI5 icon string)
 * @param string|null $icon Optional module icon for UI display.
 *
 * ### Validation rules
 * - Exactly one SemanticObject per Ui5Module.
 * - `$routes` must contain at least one intent.
 * - `$name` must be unique across all modules.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class SemanticObject
{
    /**
     * @param class-string<Model> $model Fully qualified model class (system key).
     * @param string $name Human-readable system name (English only).
     * @param array $routes Named route intents (must contain at least one).
     * @param string|null $icon Optional UI5 icon for UI representation.
     */
    public function __construct(
        public string  $model,
        public string  $name,
        public array   $routes,
        public ?string $icon = null,
    )
    {
    }
}
