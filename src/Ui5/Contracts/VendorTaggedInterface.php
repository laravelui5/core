<?php

namespace LaravelUi5\Core\Ui5\Contracts;

/**
 * Declares the vendor name of a top-level UI5 artifact.
 *
 * Used to support namespacing, multi-vendor environments,
 * and package discovery in distributed setups or marketplaces.
 */
interface VendorTaggedInterface
{
    /**
     * Returns the vendor name that owns or provides this artifact.
     *
     * @return string
     */
    public function getVendor(): string;
}
