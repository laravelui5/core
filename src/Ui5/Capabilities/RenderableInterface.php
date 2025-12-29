<?php

namespace LaravelUi5\Core\Ui5\Capabilities;

use LaravelUi5\Core\Ui5\Data\Payload;

/**
 * Defines a contract for UI5 components that can render themselves based on dynamic data.
 *
 * A Renderable component transforms structured data provided by a Provider into
 * a UI5-compatible output format (e.g., XML or JSON). This allows dynamic artifacts
 * like Tiles, Cards, or KPIs to remain declarative while consuming real-time values.
 */
interface RenderableInterface
{
    /**
     * Renders the component using the given Payload.
     *
     * The returned string must be a valid XML fragment compatible with the UI5 runtime.
     * The rendering process combines the declarative component structure with
     * dynamic values provided at runtime.
     *
     * @param Payload $data The dynamic values calculated for this artifact instance.
     * @return string XML representation of the fully composed UI5 component.
     */
    public function render(Payload $data): string;
}
