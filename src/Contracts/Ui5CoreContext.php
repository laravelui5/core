<?php

namespace LaravelUi5\Core\Contracts;

use Illuminate\Http\Request;
use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;

/**
 * Represents the technical runtime context of a UI5 artifact request.
 *
 * Access via dependency injection or the service container:
 *
 * <code>
 * $context = app(Ui5ContextInterface::class);
 * </code>
 *
 * @see ResolveUi5CoreContext
 */
final readonly class Ui5CoreContext implements Ui5ContextInterface
{
    public function __construct(
        private ?Request             $request = null,
        private Ui5ArtifactInterface $artifact,
        private ?string              $locale = null,
    )
    {
    }

    public function request(): ?Request
    {
        return $this->request;
    }

    public function artifact(): Ui5ArtifactInterface
    {
        return $this->artifact;
    }

    public function locale(): ?string
    {
        return $this->locale;
    }
}
