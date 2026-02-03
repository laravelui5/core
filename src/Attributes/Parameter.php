<?php

namespace LaravelUi5\Core\Attributes;

use Attribute;
use LaravelUi5\Core\Enums\ParameterType;

/**
 * Declarative runtime parameter definition for UI5 actions and providers.
 *
 * Parameters defined via this attribute are always resolved from
 * the action's route path segments and define the addressed
 * domain context (e.g. resource identifiers).
 *
 * Non-identifying input (payload, options, flags) MUST be provided
 * via the request body and validated using Laravel FormRequests.
 *
 * The order of Parameter attributes defines the positional order
 * of path segments.
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Parameter
{
    /**
     * @param string $name Logical parameter name (used as array key in the resolved args)
     * @param string $uriKey External transport key (path segment name, as seen by the client)
     * @param ParameterType $type Declared runtime type (drives casting/model binding)
     * @param class-string|null $model Eloquent model FQCN; required when $type = ValueType::Model
     */
    public function __construct(
        public string        $name,
        public string        $uriKey,
        public ParameterType $type,
        public ?string       $model = null,
    )
    {
    }
}
