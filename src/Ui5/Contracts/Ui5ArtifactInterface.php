<?php

namespace LaravelUi5\Core\Ui5\Contracts;

use LaravelUi5\Core\Enums\ArtifactType;

/**
 * Base interface for all UI5 artifacts such as applications, libraries, cards, or tiles.
 *
 * This interface provides the minimal contract required for an artefact to be
 * discoverable and routable within the Ui5Registry. All artefacts must declare
 * a unique JavaScript namespace and a version string to support cache busting
 * and reliable resource resolution.
 */
interface Ui5ArtifactInterface
{
    /**
     * Returns the parent module of this artifact, if any.
     *
     * - For Applications, Libraries, Cards, Reports, Tiles, KPIs, Actions and Resources:
     *   this returns the Ui5ModuleInterface instance that owns the artifact.
     * - For Dashboards (global containers outside module scope), this returns null.
     *
     * @return Ui5ModuleInterface
     */
    public function getModule(): Ui5ModuleInterface;

    /**
     * Returns the JavaScript namespace of the artifact.
     *
     * This namespace must be globally unique and is typically used as
     * the id in manifest.json as well as the key in the UI5 resource
     * root mapping (e.g. "io.pragmatiqu.tools").
     *
     * @return string
     */
    public function getNamespace(): string;

    /**
     * Returns the type of the artifact (e.g., application, library, card).
     *
     * @return ArtifactType
     */
    public function getType(): ArtifactType;

    /**
     * Returns the semantic version of the artifact.
     *
     * The version string is used to construct resource paths and control
     * client-side caching (e.g. "1.0.0").
     *
     * @return string
     */
    public function getVersion(): string;

    /**
     * Returns the localized title of the application (e.g., for manifest.json).
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Returns a short description of the application.
     *
     * @return string
     */
    public function getDescription(): string;
}
