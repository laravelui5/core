<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use LaravelUi5\Core\Contracts\Ui5ContextInterface;
use LaravelUi5\Core\Contracts\Ui5CoreContext;
use LaravelUi5\Core\Exceptions\OutdatedVersionException;
use LaravelUi5\Core\Middleware\EnsureFrontendVersionIsLatest;
use LaravelUi5\Core\Ui5\Ui5Registry;

beforeEach(function () {
    $this->middleware = new EnsureFrontendVersionIsLatest();
    $this->registry = app(Ui5Registry::class);
});

it('allows request when version matches (direct middleware test)', function () {
    $hello = $this->registry->get('com.laravelui5.hello');

    $request = Request::create('/ui5/app/hello/1.0.0/index.html');
    $route = new Route('GET', 'ui5/app/{module}/{version}/index.html', []);
    $route->bind($request);
    $request->setRouteResolver(fn () => $route);

    app()->instance(Ui5ContextInterface::class, new Ui5CoreContext($request, $hello));

    $response = $this->middleware->handle($request, fn() => response('OK', 200));
    expect($response->getStatusCode())->toBe(200);
});

it('throws OutdatedVersionException when version does not match (direct middleware test)', function () {
    $hello = $this->registry->get('com.laravelui5.hello');

    $request = Request::create('/ui5/app/hello/0.9.7/index.html');
    $route = new Route('GET', 'ui5/app/{module}/{version}/index.html', []);
    $route->bind($request);
    $request->setRouteResolver(fn () => $route);

    app()->instance(Ui5ContextInterface::class, new Ui5CoreContext($request, $hello));

    $this->middleware->handle($request, fn() => null);
})->throws(OutdatedVersionException::class);
