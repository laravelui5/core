<?php

namespace LaravelUi5\Core\Ui5\Contracts;

/**
 * Defines the contract for UI5 artifacts that can be resolved into renderable XML fragments.
 *
 * Implementations are expected to return a string of valid XML markup,
 * typically representing a <GenericTile>, <Card>, or other UI5 component structures.
 *
 * This interface abstracts the rendering logic of a specific artifact and allows it
 * to be consumed dynamically within a UI5 Fragment context (e.g., via Blade components or XML includes).
 */
interface ResolvableInterface
{
    /**
     * Resolves the UI5 artifact into a fully composed XML fragment string.
     *
     * The result must be a valid UI5 XML control definition, suitable for inclusion
     * in a <core:FragmentDefinition> layout. Any dynamic content should already be
     * injected at this point.
     *
     * @return string Rendered XML representation of the artifact.
     */
    public function resolve(): string;
}
