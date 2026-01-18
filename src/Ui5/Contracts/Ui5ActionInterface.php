<?php

namespace LaravelUi5\Core\Ui5\Contracts;

use LaravelUi5\Core\Contracts\ParameterizableInterface;
use LaravelUi5\Core\Enums\HttpMethod;
use LaravelUi5\Core\Ui5\Capabilities\ActionHandlerInterface;

/**
 * Contract for UI5 Actions.
 *
 * A UI5 Action represents a state-changing operation that can be invoked
 * from a UI5 client via the generic API dispatcher. Typical examples are:
 * - "toggle-lock" on a user
 * - "approve-invoice"
 * - "discard-draft"
 *
 * Characteristics:
 * - Actions are always *mutating* operations (never pure reads).
 * - Therefore, they must only be exposed as *POST endpoints*.
 * - Route parameters (IDs, slugs) are declared on the ActionHandler and resolved
 *   via {@see ParameterizableInterface}. The dispatcher validates and
 *   injects these before calling the handler.
 * - Body parameters (form data, payloads) are validated *inside the
 *   ActionHandler*, following Laravel best practices.
 *
 * Responsibilities:
 * - Define the action’s slug (via {@see SluggableInterface}).
 * - Provide the {@see ActionHandlerInterface} that executes the logic.
 *
 * Each Action must have a unique slug within its module. The slug is used
 * to generate the manifest entry and the API route.
 */
interface Ui5ActionInterface extends Ui5ArtifactInterface
{
    /**
     * Returns the HTTP method for calling this Action.
     *
     * Valid values:
     * - POST, verb create
     * - PATCH, verb update
     * - DELETE, verb delete.
     *
     * @return HttpMethod
     */
    public function getMethod(): HttpMethod;

    /**
     * Returns the ActionHandler responsible for executing this Action.
     *
     * Handlers encapsulate the runtime logic and must return a structured
     * result array, e.g.:
     *
     * ```php
     * ['status' => 'success', 'message' => 'Mailbox cleared']
     * ```
     *
     * @return ActionHandlerInterface
     */
    public function getHandler(): ActionHandlerInterface;
}
