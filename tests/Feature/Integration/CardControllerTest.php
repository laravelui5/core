<?php

describe('CardController', function () {
    it('serves manifest.json for card', function () {
        $response = $this->get('/ui5/card/com/laravelui5/hello/cards/work-hours@1.0.0/manifest.json');
        $response->assertStatus(200);

        $json = $response->json();
        expect($json['sap.card']['header']['title'])
            ->toBe('Work Hours')
            ->and($json['sap.card']['header']['subTitle'])
            ->toBe('Displays key data for Work Hours.')
            ->and($json['sap.card']['content']['item']['title'])
            ->toBe('Sample KPI')
            ->and($json['sap.card']['content']['item']['number'])
            ->toBe('1234.56')
            ->and($json['sap.card']['content']['item']['unit'])
            ->toBe('EUR');
    });
});
