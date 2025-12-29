<?php

namespace LaravelUi5\Core\Ui5\Capabilities;

/**
 * Defines the contract for a follow-up action that can be executed
 * as part of a UI5 Report workflow.
 *
 * Report actions are triggered by users after a report has been run,
 * and allow for executing meaningful operations on the result data
 * (e.g., flagging entries, updating records, or triggering downstream processes).
 *
 * Each action class is specific to a single report and should be
 * registered via Ui5ReportInterface::getActions().
 */
interface ReportActionInterface extends ActionHandlerInterface
{
    /**
     * Returns a short label for the action.
     *
     * Used as the button or menu label in the UI.
     *
     * Example: "Discard unbilled hours"
     *
     * @return string
     */
    public function label(): string;

    /**
     * Returns a one-line description of what the action does.
     *
     * This will be used in confirmation modals and admin overviews.
     *
     * Example: "Marks all listed hours as discarded and non-billable."
     *
     * @return string
     */
    public function description(): string;

    /**
     * Executes the action with the given report context.
     *
     * The context contains all selection parameters and filters
     * as originally passed to the report.
     *
     * The return value may be:
     * - true/false for basic success
     * - an array with structured feedback
     * - a string message to be displayed in the UI
     *
     * @param array $context
     * @return ReportActionInterface
     */
    public function withContext(array $context): self;
}
