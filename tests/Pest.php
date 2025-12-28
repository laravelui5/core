<?php

use Fixtures\Hello\Hello;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppSource;
use LaravelUi5\Core\Ui5\Ui5Registry;
use Tests\FeatureTestCase;
use Tests\UnitTestCase;

pest()->extends(FeatureTestCase::class)->in('Feature');
pest()->extends(UnitTestCase::class)->in('Unit');

function getAppSource(): Ui5AppSource
{
    $registry = Ui5Registry::fromArray(Hello::ui5Config());
    $module = $registry->getModule(Hello::SLUG);
    $app = $module->getApp();
    return $app->getSource();
}

function invokePrivateMethod(object $object, string $method, ...$args)
{
    $reflection = new ReflectionMethod($object, $method);
    $reflection->setAccessible(true);
    return $reflection->invoke($object, ...$args);
}
