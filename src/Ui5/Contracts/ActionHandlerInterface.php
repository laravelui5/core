<?php

namespace LaravelUi5\Core\Ui5\Contracts;

/**
 * Contract for UI5 Action Handlers.
 *
 * An ActionHandler encapsulates the runtime logic for a UI5 Action.
 * Unlike {@see DataProviderInterface} implementations, ActionHandlers
 * are explicitly designed to perform *state-changing operations* such as
 * - updating or deleting records
 * - triggering workflows or notifications
 * - executing domain-specific commands
 *
 * Responsibilities:
 * - Implement a single `execute()` method containing the action logic.
 * - Always return an **array** describing the outcome (machine- and
 *   human-readable), e.g.:
 *   ```php
 *   ['status' => 'success', 'message' => 'Mailbox cleared']
 *   ```
 * - May implement {@see ConfigurableInterface} to consume settings or
 *   feature flags that influence runtime behavior.
 *
 * Notes:
 * - URI parameters are resolved at the Action level (via
 *   {@see Ui5ActionInterface} + {@see ParameterizableInterface}),
 *   not on the handler itself.
 * - ActionHandlers must always return a structured result, even if
 *   no payload is strictly required (e.g., a simple status + message).
 * - Dependencies (repositories, services) should be injected via
 *   constructor dependency injection for testability and clarity.
 */
interface ActionHandlerInterface extends ExecutableInterface
{
}
