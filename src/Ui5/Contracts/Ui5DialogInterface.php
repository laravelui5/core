<?php

namespace LaravelUi5\Core\Ui5\Contracts;

/**
 * Represents a UI5 Dialog artifact that encapsulates a short-lived,
 * modal interaction context within a shell.
 *
 * Dialogs are used for focused user interactions such as creating,
 * editing, or confirming domain entities. They are globally addressable
 * via the shell (e.g. Cmd-K, actions, shortcuts), but are always
 * semantically owned by a module.
 *
 * A dialog is rendered as a UI5 view with an associated controller.
 * The surrounding shell is responsible for dialog presentation
 * (overlay, z-layer, focus handling) and for enforcing the dialog
 * lifecycle, including proper destruction after close.
 *
 * Implementations must provide the resource paths to the dialog view
 * and its controller. Data access, actions, and search helps are
 * resolved implicitly through the dialog's module context and
 * semantic relationships.
 */
interface Ui5DialogInterface extends Ui5ArtifactInterface
{
    /**
     * Returns the fully qualified UI5 view name of the dialog.
     *
     * The view is part of the dialog's owning UI5 application and
     * is rendered either by the active application itself or,
     * if the application is not active, by the Shell DialogHost.
     *
     * @return string Fully qualified UI5 view name.
     */
    public function getViewName(): string;
}
