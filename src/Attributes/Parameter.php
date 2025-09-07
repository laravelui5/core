<?php

namespace LaravelUi5\Core\Attributes;

use Attribute;
use LaravelUi5\Core\Enums\ParameterSource;
use LaravelUi5\Core\Enums\ValueType;

/**
 * Declarative runtime parameter definition for Parameterizable classes.
 *
 * Apply this attribute multiple times on a class (Ui5Action, Resource/Data provider,
 * Report provider) to describe each expected request parameter. A central
 * ParameterResolver will:
 *  - read all #[Parameter(...)] attributes,
 *  - extract raw values from the request (path/query/body),
 *  - cast them according to ParameterType,
 *  - resolve Eloquent models when type=Model,
 *  - enforce required/nullable/default semantics,
 *  - and return an immutable, normalized argument bag.
 *
 * **Key distinction**: `name` vs. `uriKey`
 * - `name`   = *logical backend name*
 *   Used as array key in the resolved argument bag and as identifier in the
 *   application code (controller, handler, service).
 *
 * - `uriKey` = *external transport key*
 *   The concrete identifier used on the communication layer (path segment or
 *   query string key). This is what the client sends, and what is reflected
 *   into the manifest.
 *
 * This separation allows:
 *  - Stable API contracts even if backend internals are refactored.
 *  - Aliasing: `uriKey` may differ from `name` (e.g. legacy URL key mapped to
 *    a cleaner internal parameter name).
 *  - Explicit, enterprise-grade clarity in manifests and generated clients.
 *
 * **Path resolution**
 * - If you use a catch-all {uri} route, `uriKey` specifies the segment name.
 * - The order of #[Parameter(...)] attributes in the source code defines the
 *   positional order of path parameters.
 *
 * **Example**
 * <code>
 *  #[Parameter(
 *      name: 'projectModel',
 *      uriKey: 'project',
 *      type: ValueType::Model,
 *      source: ParameterSource::Path,
 *      model: Project::class
 *   ),
 *   Parameter(
 *      name: 'withPositions',
 *      uriKey: 'withPositions',
 *      type: ValueType::Bool,
 *      source: ParameterSource::Query,
 *      required: false,
 *      default: false
 *   )
 *  ]
 * </code>
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Parameter
{
    /**
     * @param string               $name      Logical parameter name (used as array key in the resolved args)
     * @param string               $uriKey    External transport key (path segment or query key, as seen by the client)
     * @param ValueType            $type      Declared runtime type (drives casting/model binding)
     * @param ParameterSource      $source    Where to read from: Path, Query, Body
     * @param bool                 $required  If true and no value is provided (after source lookup), the resolver fails (400)
     * @param bool                 $nullable  Allow explicit null (distinct from “missing”); if true and null provided, no cast is applied
     * @param mixed                $default   Default used when value is missing and not required
     * @param class-string|null    $model     Eloquent model FQCN; required when $type = ValueType::Model
     */
    public function __construct(
        public string $name,
        public string $uriKey,
        public ValueType $type,
        public ParameterSource $source = ParameterSource::Path,
        public bool $required = true,
        public bool $nullable = false,
        public mixed $default = null,
        public ?string $model = null,
    ) {}
}
