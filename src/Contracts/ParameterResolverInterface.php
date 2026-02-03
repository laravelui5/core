<?php

namespace LaravelUi5\Core\Contracts;

use LaravelUi5\Core\Attributes\Parameter;
use LaravelUi5\Core\Exceptions\InvalidParameterException;
use LaravelUi5\Core\Exceptions\InvalidPathException;

/**
 * Contract for validating input parameters for UI5 artifacts.
 *
 * This interface defines a service responsible for validating and normalizing
 * the input parameters passed to a report (e.g., from the selection screen or a URL).
 *
 * Implementations must:
 * - enforce declared Parameter attributes
 * - normalize missing or default values
 * - throw validation exceptions on error
 *
 * @see Parameter
 */
interface ParameterResolverInterface
{
    /**
     * Resolve and validate all path parameters for a handler.
     *
     * This method reflects the handler's invocation contract by:
     * - reading all #[Parameter] attributes declared on the handler class,
     * - validating the request path against the declared parameter count,
     * - resolving and casting each path segment,
     * - and returning the fully resolved arguments keyed by parameter name.
     *
     * @param object $target
     *   The handler instance whose parameters should be resolved.
     *
     * @return array<string, mixed>
     *   A map of resolved argument values keyed by handler parameter name.
     *
     * @throws InvalidPathException
     *   If the number or structure of path segments does not match the
     *   declared parameter contract.
     * @throws InvalidParameterException
     *   If a path segment cannot be resolved or cast according to its
     *   parameter definition.
     */
    public function resolve(object $target): array;
}
