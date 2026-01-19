<?php

namespace LaravelUi5\Core\Middleware;

use Closure;
use Flat3\Lodata\Endpoint;
use Flat3\Lodata\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use LaravelUi5\Core\Exceptions\InvalidODataException;
use LaravelUi5\Core\Exceptions\MissingArtifactException;
use LaravelUi5\Core\Exceptions\UndefinedEndpointException;
use LaravelUi5\Core\Ui5\Contracts\Ui5RegistryInterface;

final class ResolveODataEndpoint
{
    public function __construct(
        protected Ui5RegistryInterface $registry,
    )
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $namespace = $request->route('namespace');
        $version = $request->route('version');

        if (!$namespace || !$version) {
            throw new InvalidODataException();
        }

        $key = $this->registry->pathToNamespace($namespace);

        $artifact = $this->registry->get($key);

        if (!$artifact) {
            throw new MissingArtifactException($key);
        }

        if (!$artifact instanceof Endpoint) {
            throw new UndefinedEndpointException($key);
        }

        App::instance(Endpoint::class, $artifact);

        // Discover & bind the global OData model for this endpoint
        $model = $artifact->discover(new Model());

        App::instance(Model::class, $model);

        return $next($request);
    }
}
