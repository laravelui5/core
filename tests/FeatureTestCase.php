<?php

namespace Tests;

use Fixtures\Hello\HelloModule;
use LaravelUi5\Core\Middleware\FetchCsrfToken;
use LaravelUi5\Core\Middleware\ResolveUi5Context;
use LaravelUi5\Core\Ui5CoreServiceProvider;

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
        $app['config']->set('ui5.active', 'DEV');
        $app['config']->set('lodata', [
            'prefix' => 'odata',
            'readonly' => true,
            'authorization' => false,
            'namespace' => 'com.laravelui5.hello',
            'streaming' => true,
            'disk' => 'local',
            'version' => '4.0',
            'pagination' => [
                'max' => null,
                'default' => 200,
            ]
        ]);

        $app->setBasePath(__DIR__);
    }
}
