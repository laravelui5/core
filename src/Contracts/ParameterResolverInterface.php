<?php

namespace LaravelUi5\Core\Contracts;

use LaravelUi5\Core\Attributes\Parameter;
use LaravelUi5\Core\Ui5\Contracts\ParameterizableInterface;

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
 * @see ParameterizableInterface
 */
interface ParameterResolverInterface
{
    /**
     * Validates and normalizes the report parameters.
     *
     * This method ensures that all parameters passed to the report
     * match the declared rules in the report's parameter definition.
     *
     * @param ParameterizableInterface $target The report’s data provider implementation
     */
    public function resolve(ParameterizableInterface $target): Ui5Args;
}
