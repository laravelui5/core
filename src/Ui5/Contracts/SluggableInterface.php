<?php

namespace LaravelUi5\Core\Ui5\Contracts;

/**
 * Contract for all UI5 entities that can be addressed via a URL segment (slug).
 *
 * A slug is a short, URL-safe identifier used to generate client routes and
 * bind server-side endpoints. It is unique within the context of its parent,
 * typically a module, and forms part of the full `url_key` (e.g. "card/core/budget").
 *
 * All sluggable artifacts must implement this interface to be routable from
 * the UI or resolvable in policies, manifests, or database records.
 *
 * Examples:
 * - "app/hello" → slug = "hello"
 * - "card/budget" → slug = "budget"
 * - "api/user/toggle-lock" → slug = "toggle-lock"
 *
 * The slug should be URL-friendly (lowercase, kebab-case) and stable.
 */
interface SluggableInterface
{
    /**
     * Returns the slug that identifies this artifact/module/action within its context.
     *
     * This value is used to generate the `url_key`, which serves as a unified
     * identifier across the client (routing), server (controllers), registry,
     * and admin/database layers.
     *
     * @return string A stable, kebab-case string (e.g. "toggle-lock", "budget-report")
     */
    public function getSlug(): string;
}
