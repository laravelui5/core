<?php

namespace LaravelUi5\Core\Introspection\App;

/**
 * Represents a UI5 routing entry as declared in the application manifest.
 *
 * This class intentionally models only the semantically relevant subset of a
 * route definition for SDK-level introspection:
 *
 *  - `name`    : the stable identifier of the route,
 *  - `pattern` : the URL pattern used for navigation,
 *  - `target`  : one or more target keys activated by the route.
 *
 * All additional UI5 routing options (e.g. transitions, callbacks, layouts)
 * are deliberately ignored, as they are runtime concerns and not required for
 * structural analysis or tooling purposes.
 *
 * @see https://sdk.openui5.org/#/topic/902313063d6f45aeaa3388cc4c13c34e
 */
final readonly class Ui5Route
{
    public function __construct(
        public string       $name,
        public string       $pattern,
        public string|array $target,
    )
    {
    }

    /**
     * Returns whether the route pattern contains dynamic parameters.
     *
     * UI5 supports two parameter syntaxes in route patterns:
     *  - legacy syntax: :param:
     *  - modern syntax: {param}
     *
     * Routes containing either form are considered parameterized.
     */
    public function hasParameters(): bool
    {
        return str_contains($this->pattern, ':')
            || str_contains($this->pattern, '{');
    }
}
