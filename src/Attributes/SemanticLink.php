<?php

namespace LaravelUi5\Core\Attributes;

use Attribute;

/**
 * Declares a semantic link between two {@see SemanticObject}s.
 *
 * Applied to a property of a model that itself represents a SemanticObject,
 * this attribute marks a *reference* (arc) to another semantic object.
 *
 * The link enables the SDK and UI to automatically:
 *  - resolve cross-module navigation intents
 *  - display related-object menus and context actions
 *  - construct semantic relationship graphs
 *
 * ### Example
 * ```php
 * use LaravelUi5\Core\Attributes\SemanticLink;
 *
 * class Project extends Model
 * {
 *     #[SemanticLink(model: \Ui5\Partners\Models\Partner::class)]
 *     public string $partner_id;
 * }
 * ```
 *
 * ### Parameters
 * @param string $model  Fully qualified Eloquent model class of the
 *                       *target* semantic object.
 *
 * ### Validation rules
 * - May only be placed on models that are declared as a SemanticObject.
 * - `$model` must reference another registered SemanticObject model.
 * - Links to unknown models cause a LogicException during pass 2.
 *
 * ### Design rationale
 * The link does *not* contain a title or icon. Those are resolved automatically
 * from the target semantic object, ensuring a single source of truth and
 * a consistent naming graph across the system.
 */
#[Attribute(Attribute::TARGET_METHOD)]
class SemanticLink
{
    /**
     * @param string $model Fully qualified model class of the target
     *                      SemanticObject. Must exist and be discoverable.
     */
    public function __construct(
        public string $model,
    ) {}
}
