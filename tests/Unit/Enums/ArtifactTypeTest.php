<?php

use LaravelUi5\Core\Enums\ArtifactType;
use LaravelUi5\Core\Exceptions\InvalidPathException;
use Tests\Fixture\Hello\HelloModule;

function getModule(): HelloModule
{
    return new HelloModule('hello');
}

it('generates urlKey from app artifact', function () {
    $app = getModule()->getApp();
    $key = ArtifactType::urlKeyFromArtifact($app);

    expect($key)->toBe('app/hello');
});

it('generates urlKey from library artifact', function () {
    $app = getModule()->getLibrary();
    $key = ArtifactType::urlKeyFromArtifact($app);

    expect($key)->toBe('lib/hello');
});

it('generates urlKey from action artifact', function () {
    $action = collect(getModule()->getActions())->first();
    $key = ArtifactType::urlKeyFromArtifact($action);

    expect($key)->toBe('api/hello/world');
});

it('generates urlKey from resource artifact', function () {
    $resource = collect(getModule()->getResources())->first();
    $key = ArtifactType::urlKeyFromArtifact($resource);

    expect($key)->toBe('resource/hello/first');
});

it('generates urlKey from card artifact', function () {
    $card = collect(getModule()->getCards())->first();
    $key = ArtifactType::urlKeyFromArtifact($card);

    expect($key)->toBe('card/hello/work-hours');
});

it('generates urlKey from report artifact', function () {
    $report = new Tests\Fixture\Hello\Reports\World\Report();
    $key = ArtifactType::urlKeyFromArtifact($report);

    expect($key)->toBe('report/hello-world-report');
});

it('parses urlKey from app path', function () {
    $path = 'app/hello';
    $key = ArtifactType::urlKeyFromPath($path);

    expect($key)->toBe('app/hello');
});

it('parses urlKey from lib path', function () {
    $path = 'lib/hello';
    $key = ArtifactType::urlKeyFromPath($path);

    expect($key)->toBe('lib/hello');
});

it('parses urlKey from action path', function () {
    $path = 'api/hello/world';
    $key = ArtifactType::urlKeyFromPath($path);

    expect($key)->toBe('api/hello/world');
});

it('parses urlKey from resource path', function () {
    $path = 'resource/hello/first';
    $key = ArtifactType::urlKeyFromPath($path);

    expect($key)->toBe('resource/hello/first');
});

it('parses urlKey from card path', function () {
    $path = 'card/hello/work-hours';
    $key = ArtifactType::urlKeyFromPath($path);

    expect($key)->toBe('card/hello/work-hours');
});

it('parses urlKey from report path', function () {
    $path = 'report/hello-world';
    $key = ArtifactType::urlKeyFromPath($path);

    expect($key)->toBe('report/hello-world');
});

it('throws for invalid path with too few segments', function () {
    ArtifactType::urlKeyFromPath('app');
})->throws(InvalidPathException::class);

it('throws for unknown artifact type', function () {
    ArtifactType::urlKeyFromPath('foo/bar');
})->throws(InvalidPathException::class);
