<?php

namespace LaravelUi5\Core\Ui5\Contracts;

/**
 * Represents a UI5 routing target reduced to its navigational identity.
 *
 * As a result, the target is reduced to:
 *
 *  - `key`  : the target identifier as referenced by routes,
 *  - `name` : the resolved view name representing the actual page.
 *
 * All other target configuration options (e.g. control placement, aggregation,
 * transitions) are considered runtime-specific and are therefore excluded.
 *
 * @see https://sdk.openui5.org/#/topic/902313063d6f45aeaa3388cc4c13c34e
 */
final readonly class Ui5Target
{
    public function __construct(
        public string $key,
        public string $name,
    )
    {
    }
}
