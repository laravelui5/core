<?php

describe('ResourceController', function () {
    it('handles GET correctly', function () {
        $response = $this->get('/ui5/resource/com/laravelui5/hello/resources/first@1.0.0');
        $response->assertStatus(200);

        $json = $response->json();
        expect($json['status'])
            ->toBe('Success')
            ->and($json['message'])
            ->toContain('The resource was aggregated successfully.');
    });
});
