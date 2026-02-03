<?php

use LaravelUi5\Core\Introspection\App\Ui5AppDescriptor;
use LaravelUi5\Core\Introspection\App\Ui5AppSource;
use LaravelUi5\Core\Introspection\App\Ui5Route;
use LaravelUi5\Core\Introspection\App\Ui5Target;

describe('ManifestController', function () {
    it('serves a valid ui5 app manifest.json', function () {
        $response = $this->get('/ui5/app/com/laravelui5/hello@1.0.0/manifest.json');
        $response->assertStatus(200);

        $json = $response->json();

        expect($json['sap.app']['id'])
            ->toBe('io.pragmatiqu.portal')
            ->and($json['sap.app']['dataSources']['mainService']['uri'])
            ->toBe('http://localhost/odata/com/laravelui5/hello@1.0.0/')
            ->and($json['sap.app']['dataSources']['mainService']['type'])
            ->toBe('OData')
            ->and($json['sap.app']['dataSources']['mainService']['settings']['odataVersion'])
            ->toBe('4.0')
            ->and($json['sap.ui5']['models']['']['dataSource'])
            ->toBe('mainService')
            ->and($json['sap.ui5']['models']['']['settings']['operationMode'])
            ->toBe('Server')
            ->and($json['sap.ui5']['models']['']['settings']['earlyRequests'])
            ->toBeTrue()
            ->and($json['sap.ui5']['rootView']['viewName'])
            ->toBe('io.pragmatiqu.portal.view.App')
            ->and($json['laravel.ui5']['actions']['com.laravelui5.hello.actions.world']['method'])
            ->toBe('POST')
            ->and($json['laravel.ui5']['actions']['com.laravelui5.hello.actions.world']['url'])
            ->toBe('/ui5/api/com/laravelui5/hello/actions/world@1.0.0/')
            ->and($json['laravel.ui5']['resources']['com.laravelui5.hello.resources.first']['method'])
            ->toBe('GET')
            ->and($json['laravel.ui5']['settings']['darkMode'])->toBeFalse()
            ->and($json['laravel.ui5']['settings']['maxItems'])->toBe(10);
    });
});
