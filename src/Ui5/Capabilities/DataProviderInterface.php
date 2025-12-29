<?php

namespace LaravelUi5\Core\Ui5\Capabilities;

use LaravelUi5\Core\Contracts\ConfigurableInterface;
use LaravelUi5\Core\Contracts\ParameterizableInterface;

/**
 * Contract for UI5 Data Providers.
 *
 * A DataProvider encapsulates read-only, idempotent logic for delivering
 * structured data to UI5 artifacts such as Cards, Resources, or Reports.
 *
 * Responsibilities:
 * - Assemble domain-specific data in a backend-driven way.
 * - Return results in a structured array suitable for JSON serialization.
 * - Optionally implement {@see ParameterizableInterface} to receive
 *   validated request parameters.
 * - Optionally implement {@see ConfigurableInterface} to receive resolved
 *   tenant- or artifact-specific settings.
 *
 * Notes:
 * - DataProviders must not perform state-changing operations.
 * - Dependencies (services, repositories) should be injected via constructor DI.
 * - The return array should be normalized (arrays, scalars, nested objects),
 *   not raw models or resources.
 */
interface DataProviderInterface extends ExecutableInterface
{
}
