<?php

namespace LaravelUi5\Core\Contracts;

/**
 * Contract for classes that consume declarative settings/configuration.
 *
 * Implementations must support an immutable "wither" to inject a typed
 * configuration bag (Ui5Config) and expose a read accessor for usage
 * in domain code.
 *
 * Notes:
 * - In Core, the resolver may provide defaults only.
 * - In the SDK, a database-backed resolver can supply effective values
 *   (scope precedence, casting).
 *
 * Rules:
 * - withConfig(Ui5Config) MUST NOT mutate the instance; return a clone.
 * - config() MUST always return a Ui5Config (empty bag if nothing injected).
 *
 * Typical implementers:
 * - Resource/Report providers that read feature flags or limits
 * - Action handlers with tunable behavior
 */
interface ConfigurableInterface
{
    /**
     * Immutable injection of resolved configuration.
     *
     * @return static cloned instance carrying the provided arguments
     */
    public function withConfig(Ui5Config $config): static;

    /**
     * Accessor for the resolved configuration.
     * Should return an empty Ui5Config when no config was injected.
     */
    public function config(): Ui5Config;
}
