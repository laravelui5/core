<?php

namespace LaravelUi5\Core\Ui5\Capabilities;

/**
 * Contract for UI5 Data Providers.
 *
 * A DataProvider encapsulates read-only, idempotent logic for delivering
 * structured data to UI5 artifacts such as Cards, Resources, or Reports.
 *
 * Responsibilities:
 * - Assemble domain-specific data in a backend-driven way.
 * - Return results in a structured array suitable for JSON serialization.
 *
 * Notes:
 * - DataProviders must not perform state-changing operations.
 * - Dependencies (services, repositories) should be injected via constructor DI.
 * - The return array should be normalized (arrays, scalars, nested objects),
 *   not raw models or resources.
 */
interface DataProviderInterface
{
}
