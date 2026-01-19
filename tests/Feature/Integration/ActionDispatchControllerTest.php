<?php

describe('ActionDispatchController', function () {
    it('serves i18n properties for ui5 app', function () {
        $response = $this->post('/ui5/api/com/laravelui5/hello/actions/world@1.0.0');
        $response->assertStatus(200);

        $json = $response->json();
        expect($json['status'])
            ->toBe('Success')
            ->and($json['message'])
            ->toContain('The action was executed successfully.');
    });
});
