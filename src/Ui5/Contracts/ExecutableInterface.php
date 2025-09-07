<?php

namespace LaravelUi5\Core\Ui5\Contracts;

/**
 * Generic contract for any executable UI5 component.
 *
 * This interface unifies the runtime contract for classes that can be
 * *executed* by the framework, such as:
 *
 *  - {@see DataProviderInterface}: read-only providers that return
 *    structured data (e.g., for Cards, Resources, Reports).
 *  - {@see ActionHandlerInterface}: state-changing handlers that perform
 *    mutations or trigger workflows (e.g. "toggle-lock", "approve-invoice").
 *
 * Having this shared contract allows controllers and orchestration
 * services to interact with both providers and handlers in a uniform way,
 * while still preserving semantic separation at the type level.
 *
 * ### Design notes
 * - The return type is:
 *   - *array<string,mixed>* for structured data results.
 * - Cross-cutting concerns (parameter resolution, configuration injection)
 *   are handled externally via {@see ParameterizableInterface} and
 *   {@see ConfigurableInterface}.
 * - Dependencies (repositories, services) should be provided via
 *   constructor DI; do not inject via the `execute()` method.
 */
interface ExecutableInterface
{
    /**
     * Execute the component logic and return the result.
     *
     * @return array<string,mixed> Structured data.
     */
    public function execute(): array;
}
