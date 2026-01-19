<?php

use LaravelUi5\Core\Introspection\App\Ui5AppDescriptor;
use LaravelUi5\Core\Introspection\App\Ui5AppSource;
use LaravelUi5\Core\Introspection\App\Ui5Route;
use LaravelUi5\Core\Introspection\App\Ui5Target;

describe('index.html', function () {
    it('serves ui5 app index.html', function () {
        $response = $this->get('/ui5/app/com/laravelui5/hello@1.0.0/index.html');

        $response
            ->assertStatus(200)
            ->assertSee('<!DOCTYPE html>', false)
            ->assertSee('<html', false)
            ->assertSee('<head>', false)
            ->assertSee('<body', false)
            ->assertSee('id="sap-ui-bootstrap"', false)
            ->assertSee('sap-ui-core.js', false)
            ->assertSee('data-sap-ui-async="true"', false)
            ->assertSee('data-sap-ui-theme="sap_horizon"', false)
            ->assertSee('"com.laravelui5.hello":"./"', false)
            ->assertSee('"com.laravelui5.core":"/ui5/lib/com/laravelui5/core@1.0.0"', false)
            ->assertSee('data-sap-ui-oninit="module:com/laravelui5/hello/Component"', false)
            ->assertSee('name: "com.laravelui5.hello"', false)
            ->assertSee('<div id="content" data-sap-ui-component data-name="com.laravelui5.hello">', false);
    });
});
