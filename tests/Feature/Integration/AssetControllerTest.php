<?php

it('serves i18n properties for ui5 app', function () {
    $response = $this->get(
        '/ui5/app/com/laravelui5/hello@1.0.0/i18n/i18n.properties'
    );

    $response->assertStatus(200);

    $file = $response->baseResponse->getFile();
    $content = file_get_contents($file->getPathname());

    expect($content)->toContain('appTitle=LaravelUi5/Core')
        ->and($content)->toContain('appDescription=Test fixtures for the LaravelUi5/Core package')
        ->and($content)->toContain('Fallback Locale')
        ->and($content)->not->toContain('<html');
        // Safety: ensure this is not HTML
});
