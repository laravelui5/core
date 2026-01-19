<?php

describe('DashboardController', function () {
    it('serves manifest.json for card', function () {
        $response = $this->get('/ui5/app/com/laravelui5/dashboard/com/laravelui5/hello/dashboards/world@1.0.0/view/Dashboard.view.xml');
        $response->assertStatus(200);

        $xml = $response->getContent();
        expect($xml)->toBeString()
            ->toContain('<mvc:View')
            ->toContain('controllerName="io.pragmatiqu.dashboard.controller.Dashboard"')
            ->toContain('<f:GridContainer')
            ->toContain('<GenericTile')
            ->toContain('<NumericContent')
            ->toContain('press="onPress"')
            ->toContain('title="{i18n>appTitle}"')
            ->not->toContain('<html')
            ->not->toContain('@extends')
            ->and(substr_count($xml, '<GenericTile'))->toBeGreaterThan(5);
    });
});
