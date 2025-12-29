<?php

namespace LaravelUi5\Core\Ui5\Contracts;

use LaravelUi5\Core\Contracts\ConfigurableInterface;
use LaravelUi5\Core\Contracts\ParameterizableInterface;
use LaravelUi5\Core\Ui5\Capabilities\DataProviderInterface;
use LaravelUi5\Core\Ui5\Capabilities\SluggableInterface;

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
 * - Provides routing information via {@see SluggableInterface}.
 * - Acts as a discoverable artifact within the {@see Ui5RegistryInterface}.
 *
 * Cross-cutting concerns:
 * - If the provider implements {@see ParameterizableInterface}, validated
 *   request parameters are injected before execution.
 * - If the provider implements {@see ConfigurableInterface}, resolved
 *   settings are injected before execution.
 *
 * Cards are read-only artifacts: they must not change application state.
 */
interface Ui5CardInterface extends Ui5ArtifactInterface, SluggableInterface
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
}
