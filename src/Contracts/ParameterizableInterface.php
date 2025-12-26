<?php

namespace LaravelUi5\Core\Contracts;


/**
 * Contract for classes that accept declarative, resolved request parameters.
 *
 * Implementations must support an immutable "wither" to inject a typed
 * argument bag (Ui5Args) and expose a read accessor for usage in domain code.
 *
 * Rules:
 * - withArgs(Ui5Args) MUST NOT mutate the instance; return a cloned instance.
 * - args() MUST always return a Ui5Args (empty bag if nothing was injected).
 *
 * Typical implementers:
 * - Report data providers
 * - Resource data providers
 * - Action handlers
 */
interface ParameterizableInterface
{
    /**
     * Immutable injection of resolved parameters.
     *
     * @return static cloned instance carrying the provided arguments
     */
    public function withArgs(Ui5Args $args): static;

    /**
     * Accessor for the resolved arguments.
     * Should return an empty Ui5Args when no arguments were injected.
     */
    public function args(): Ui5Args;
}
