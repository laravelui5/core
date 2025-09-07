<?php

namespace LaravelUi5\Core\Enums;

/**
 * Declares where a parameter is sourced from in the request.
 *
 * *Body* parameters are intentionally not supported here.
 *
 * Actions should validate request bodies via Laravel’s native
 * `$request->validate()` inside the ActionHandler.
 *
 * This separation ensures clean semantics
 * - *Route/Query* → declarative parameters (resolved upfront).
 * - *Body* → payload handled by Laravel validation.
 */
enum ParameterSource: int
{
    /**
     * Extracted from route segments.
     *
     * - Always allowed (Actions, Reports, Resources).
     * - Typical usage: identifiers (`/api/user/42/toggle-lock`).
     */
    case Path  = 1;

    /**
     * Extracted from the query string (`?from=2024-01-01&to=2024-12-31`).
     *
     * - Allowed for *GET-based providers* (Reports, Resources).
     * - *Forbidden for Actions* (POST).
     *
     * Controllers should enforce this rule and abort with 400 if a `Query`
     * parameter is defined on an Action.
     */
    case Query = 2;

    /**
     * Returns a symbolic label, e.g., 'Path'.
     */
    public function label(): string
    {
        return match ($this) {
            self::Path  => 'Path',
            self::Query => 'Query',
        };
    }
}
