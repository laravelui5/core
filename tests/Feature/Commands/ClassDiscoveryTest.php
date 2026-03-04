<?php

use Illuminate\Filesystem\Filesystem;
use LaravelUi5\Core\Commands\GenerateUi5Action;

it('extracts the fully qualified class name from fixture', function () {

    $fixture = __DIR__ . '/../../../tests-fixture/src/HelloModule.php';

    $command = new GenerateUi5Action(new Filesystem());

    $method = new ReflectionMethod($command, 'getNamespaceFromFile');
    $method->setAccessible(true);

    $fqn = $method->invoke($command, $fixture);

    expect($fqn)->toBe('Fixtures\\Hello');
});
