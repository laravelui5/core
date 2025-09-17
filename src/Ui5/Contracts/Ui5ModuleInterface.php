<?php

namespace LaravelUi5\Core\Ui5\Contracts;

/**
 * A Ui5Module represents the root-level container for all UI5-related artifacts
 * provided by a feature package or domain module in the LaravelUi5 ecosystem.
 *
 * A module can contain either a UI5 application or a library, but not both.
 * Only applications can provide subordinate artifacts such as cards, actions,
 * reports, KPIs, and tiles.
 *
 * Responsibilities:
 * - Expose the main artifact (App or Library) via type-specific getters
 * - Declare all subordinate artifacts (if applicable)
 * - Provide artifact metadata via Ui5ArtifactInterface
 *
 * System rules:
 * - A module MUST provide either an app or a library (exclusive)
 * - A library MUST NOT define subordinate artifacts
 * - Every artifact must have a globally unique namespace
 */
interface Ui5ModuleInterface extends SluggableInterface
{
    /**
     * Returns true if this module provides a UI5 application.
     *
     * @return bool
     */
    public function hasApp(): bool;

    /**
     * Returns the application artifact, if present.
     *
     * @return Ui5AppInterface|null
     */
    public function getApp(): ?Ui5AppInterface;

    /**
     * Returns true if this module provides a UI5 library.
     *
     * @return bool
     */
    public function hasLibrary(): bool;

    /**
     * Returns the library artifact, if present.
     *
     * @return Ui5LibraryInterface|null
     */
    public function getLibrary(): ?Ui5LibraryInterface;

    /**
     * Returns the root artifact of the module — either the application or the library,
     * depending on the module type.
     *
     * This method allows consumers to access the primary artifact in a generic way,
     * without checking whether the module is app- or library-based.
     *
     * @return Ui5ArtifactInterface The root artifact (App or Library)
     */
    public function getArtifactRoot(): Ui5ArtifactInterface;

    /**
     * Returns an array of all cards provided by this module.
     * Only available if this module provides an app.
     *
     * @return Ui5CardInterface[]
     */
    public function getCards(): array;

    /**
     * Returns an array of all KPIs provided by this module.
     *
     * @return Ui5KpiInterface[]
     */
    public function getKpis(): array;

    /**
     * Returns an array of all tiles for launchpad or navigation purposes.
     *
     * @return Ui5TileInterface[]
     */
    public function getTiles(): array;

    /**
     * Returns an array of all actions (API endpoints) provided by this module.
     *
     * @return Ui5ActionInterface[]
     */
    public function getActions(): array;

    /**
     * Returns an array of all resources (API) provided by this module.
     *
     * @return Ui5ResourceInterface[]
     */
    public function getResources(): array;
}
