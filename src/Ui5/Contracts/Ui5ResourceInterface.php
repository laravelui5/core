<?php

namespace LaravelUi5\Core\Ui5\Contracts;

/**
 * Contract for discoverable UI5 Resources.
 *
 * A Resource is a lightweight, read-only UI5 artifact that exposes
 * structured data for consumption by the client (e.g. cards, dashboards, lists).
 *
 * Responsibilities:
 * - Declares its identity (slug, urlKey) via Ui5ArtifactInterface + SluggableInterface.
 * - Provides access to its runtime logic via a ResourceDataProvider.
 *
 * The actual execution logic lives in the ResourceDataProvider, keeping
 * metadata and runtime separate.
 */
interface Ui5ResourceInterface extends Ui5ArtifactInterface, SluggableInterface
{

    /**
     * Returns the data provider that implements the runtime logic for this resource.
     *
     * @return DataProviderInterface
     */
    public function getProvider(): DataProviderInterface;
}
