<?php

namespace LaravelUi5\Core\Contracts;

use Illuminate\Http\Request;
use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;

/**
 * Interface Ui5ContextInterface
 *
 * Defines the minimal technical runtime context contract for LaravelUi5.
 *
 * The context represents the *current execution scope* of a UI5 request
 * and provides access to the technical information required by the Core
 * to resolve and serve UI5 artifacts.
 *
 * This interface is intentionally minimal and semantically neutral.
 * It must not expose business concepts, authorization data, or
 * identity-related information.
 *
 * ---------------------------------------------------------------------
 * Design principles
 * ---------------------------------------------------------------------
 * - Contract-first: consumers depend on this interface, not on a concrete class
 * - Minimal surface: only information required by Core is exposed
 * - Immutable by convention: one context per logical execution
 * - Extensible: higher layers may provide richer implementations
 *
 * ---------------------------------------------------------------------
 * Scope
 * ---------------------------------------------------------------------
 * Implementations of this interface may be created:
 *  - from HTTP requests (via middleware)
 *  - in console commands, jobs, or tests (without a Request)
 *
 * The actual implementation bound in the service container
 * may vary depending on the active layer (Core-only or SDK-extended).
 *
 * ---------------------------------------------------------------------
 * Responsibilities
 * ---------------------------------------------------------------------
 * The context provides:
 *  - access to the current HTTP request (if any)
 *  - access to the resolved UI5 artifact addressed by the URI
 *  - access to the effective locale for technical rendering
 *
 * The context does NOT:
 *  - perform authorization
 *  - represent tenants or users
 *  - interpret semantic meaning
 *  - resolve or dispatch intents
 *
 * @package LaravelUi5\Core\Contracts
 */
interface Ui5ContextInterface
{
    /**
     * Returns the current HTTP request, if available.
     *
     * Implementations must return null when the context
     * was created outside of an HTTP lifecycle
     * (e.g. console commands, queue jobs, tests).
     *
     * @return Request|null
     */
    public function request(): ?Request;

    /**
     * Returns the UI5 artifact resolved for the current execution.
     *
     * This artifact is determined by the Core based on the request URI
     * and represents the technical entry point being served
     * (e.g. App, Library, Report, Action).
     *
     * @return Ui5ArtifactInterface
     */
    public function artifact(): Ui5ArtifactInterface;

    /**
     * Returns the effective locale for this execution, if defined.
     *
     * The locale is used for technical rendering purposes
     * (e.g. UI5 bootstrap, resource resolution).
     *
     * Implementations may return null when locale resolution
     * is handled elsewhere.
     *
     * @return string|null
     */
    public function locale(): ?string;
}
