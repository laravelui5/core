<?php

namespace LaravelUi5\Core\Ui5\Contracts;

use LaravelUi5\Core\Ui5\Capabilities\DataProviderInterface;

/**
 * Contract for UI5 Card artifacts.
 *
 * A Card is a lightweight, embeddable UI element that typically displays
 * summary or entry-point information (e.g. KPIs, recent items, small lists).
 * It is backed by a {@see DataProviderInterface}, which encapsulates the
 * domain logic and delivers the structured data required for rendering.
 *
 * Responsibilities:
 * - Declares the association between the Card artifact and its provider.
 * - Acts as a discoverable artifact within the {@see Ui5RegistryInterface}.
 *
 * Cards are read-only artifacts: they must not change application state.
 */
interface Ui5CardInterface extends Ui5ArtifactInterface
{
    /**
     * Returns the class name or instance of the associated DataProvider.
     *
     * The provider is responsible for delivering the `card.content.data`
     * structure as defined in the card manifest.
     *
     * @return DataProviderInterface
     */
    public function getProvider(): DataProviderInterface;

    /**
     * Returns the realized manifest.json Blade component as string.
     *
     * @return string
     */
    public function getManifest(): string;
}
