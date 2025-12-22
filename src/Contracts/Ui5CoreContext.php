<?php

namespace LaravelUi5\Core\Contracts;

use Illuminate\Http\Request;
use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;

/**
 * Represents the technical runtime context of a UI5 artifact request.
 *
 * The Ui5CoreContext encapsulates only the information required by the
 * LaravelUi5 Core to resolve, route, and serve UI5 artifacts in a
 * deterministic and framework-agnostic way.
 *
 * This context is intentionally minimal and strictly technical.
 * It does NOT carry any semantic, authorization, or business-level data.
 *
 * ---------------------------------------------------------------------
 * Scope
 * ---------------------------------------------------------------------
 * - HTTP requests:
 *   Constructed by middleware based on the current Request and Route.
 *
 * - Console / Queue / Tests:
 *   May be created manually without an HTTP Request.
 *
 * ---------------------------------------------------------------------
 * Responsibilities
 * ---------------------------------------------------------------------
 * The core context provides:
 *  - The current HTTP request (if any)
 *  - The resolved UI5 artifact addressed by the URI
 *  - The effective locale for technical rendering purposes
 *
 * The core context does NOT:
 *  - identify tenants or users
 *  - represent acting or authenticated principals
 *  - perform authorization
 *  - interpret semantic meaning
 *  - participate in intent resolution or dispatch
 *
 * ---------------------------------------------------------------------
 * Design principles
 * ---------------------------------------------------------------------
 * - Immutable: one context instance per logical execution
 * - Explicit: all data is provided via constructor arguments
 * - Neutral: free of business, authorization, or navigation semantics
 *
 * Access via dependency injection or the service container:
 *
 * <code>
 * $context = app(Ui5CoreContext::class);
 * </code>
 *
 * @see ResolveUi5CoreContext
 */
final readonly class Ui5CoreContext
{
    /**
     * @param Request|null $request
     *        The current HTTP request, or null when executed outside
     *        of an HTTP lifecycle (e.g. CLI, queue, tests).
     *
     * @param Ui5ArtifactInterface $artifact
     *        The UI5 artifact resolved from the request URI.
     *
     * @param string|null $locale
     *        The effective locale for this request, if explicitly set.
     *        May be null when locale resolution is handled elsewhere.
     */
    public function __construct(
        public ?Request             $request = null,
        public Ui5ArtifactInterface $artifact,
        public ?string              $locale = null,
    )
    {
    }
}
