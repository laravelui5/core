<?php

use Tests\FeatureTestCase;
use Tests\UnitTestCase;

pest()->extends(FeatureTestCase::class)->in('Feature');
pest()->extends(UnitTestCase::class)->in('Unit');

function invokePrivateMethod(object $object, string $method, ...$args)
{
    $reflection = new ReflectionMethod($object, $method);
    $reflection->setAccessible(true);
    return $reflection->invoke($object, ...$args);
}
