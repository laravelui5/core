<?php

use Illuminate\Http\Request;
use LaravelUi5\Core\Contracts\Ui5ContextInterface;
use LaravelUi5\Core\Middleware\ResolveUi5Context;
use function Pest\Laravel\get;

it('does not register Ui5Context in the container for non-ui5 routes', function () {
    get('/foo/bar')->assertStatus(404);
    expect(app()->bound(Ui5ContextInterface::class))->toBeFalse();
});

it('does register Ui5Context in the container for ui5 routes', function () {
    $request = Request::create('/ui5/app/hello/1.0.0/index.html', 'GET');

    $middleware = app(ResolveUi5Context::class);

    $middleware->handle($request, function ($req) {
        expect(app()->bound(Ui5ContextInterface::class))->toBeTrue();
        $context = app(Ui5ContextInterface::class);
        expect($context->artifact()->getNamespace())->toBe('com.laravelui5.hello');
        return response('OK');
    });
});

it('returns 404 for unknown ui5 path', function () {
    get('/ui5/unknown')->assertStatus(404);
});
