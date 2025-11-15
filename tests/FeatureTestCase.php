<?php

namespace Tests;

use LaravelUi5\Core\Middleware\FetchCsrfToken;
use LaravelUi5\Core\Middleware\ResolveUi5Context;
use LaravelUi5\Core\Ui5CoreServiceProvider;
use Tests\Fixture\Hello\HelloModule;

class FeatureTestCase extends \Orchestra\Testbench\TestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['router']->pushMiddlewareToGroup('web', FetchCsrfToken::class);
        $this->app['router']->pushMiddlewareToGroup('web', ResolveUi5Context::class);
    }

    protected function getPackageProviders($app): array
    {
        return [
            Ui5CoreServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $modules = $app['config']->get('ui5.modules', []);
        $modules['hello'] = HelloModule::class;
        $app['config']->set('ui5.modules', $modules);
    }
}
