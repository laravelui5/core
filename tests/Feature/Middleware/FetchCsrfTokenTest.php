<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use LaravelUi5\Core\Middleware\FetchCsrfToken;

it('does not add csrf token header if request does not ask for it', function () {
    $request = Request::create('/odata/Users', 'GET'); // ganz normaler Request

    $middleware = new FetchCsrfToken();

    $response = $middleware->handle($request, fn () => new Response('ok'));

    expect($response->headers->has('X-CSRF-Token'))->toBeFalse();
});

it('adds csrf token header if request asks with Fetch', function () {
    $request = Request::create('/odata/Users', 'GET', [], [], [], [
        'HTTP_X-CSRF-Token' => 'Fetch',
    ]);

    $middleware = new FetchCsrfToken();

    $response = $middleware->handle($request, fn () => new Response('ok'));

    expect($response->headers->get('X-CSRF-Token'))->toBe(csrf_token());
});
