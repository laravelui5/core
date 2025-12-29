<?php

namespace LaravelUi5\Core\Ui5\Capabilities;

use LaravelUi5\Core\Contracts\ConfigurableInterface;
use LaravelUi5\Core\Contracts\ParameterizableInterface;

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
 * - Input expectations are declared via {@see ParameterizableInterface},
 * - ActionHandlers must always return a structured result, even if
 *   no payload is strictly required (e.g., a simple status + message).
 * - Dependencies (repositories, services) should be injected via
 *   constructor dependency injection for testability and clarity.
 */
interface ActionHandlerInterface extends ExecutableInterface, ParameterizableInterface
{
}
