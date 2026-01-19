<?php

describe('ODataController', function () {
    it('serves $manifest for app', function () {
        $response = $this->get('/odata/com/laravelui5/hello@1.0.0/$metadata');
        $response->assertStatus(200);

        $xml = $response->streamedContent();

        expect($xml)
            ->toBeString()
            ->toContain('<edmx:Edmx')
            ->toContain('Version="4.0"')
            ->toContain('Namespace="com.laravelui5.hello"')
            ->toContain('DefaultContainer')
            ->toContain('Org.OData.Core.V1')
            ->toContain('Org.OData.Capabilities.V1')
            ->toContain('application/json')
            ->toContain('application/xml')
            ->not->toContain('<html')
            ->not->toContain('{');
    });
});
