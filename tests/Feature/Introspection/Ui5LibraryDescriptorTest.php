<?php

use LaravelUi5\Core\Introspection\Library\Ui5LibraryDescriptor;

describe('Ui5LibraryDescriptor', function () {
    it('resolves metadata from .library', function () {
        $descriptor = Ui5LibraryDescriptor::fromLibraryXml(base_path('/../tests-fixture/ui5-hello-lib'), 'com.laravelui5.core', 'npm run build');
        $dependencies = $descriptor->getDependencies();
        expect($descriptor)
            ->toBeInstanceOf(Ui5LibraryDescriptor::class)
            ->and($descriptor->getVendor())
            ->toBe('Pragmatiqu IT GmbH')
            ->and($descriptor->getVersion())
            ->toBe('1.0.0')
            ->and($descriptor->getTitle())
            ->toBe('Laravel Ui5 Core Library')
            ->and($descriptor->getDescription())
            ->toBe('Takes care of the hard parts of integrating UI5 with Laravel: secure CSRF handling, session-aware fetch calls, and a clean way to connect your UIComponent to a backend service—ready to use, no hassle.')
            ->and($descriptor->getNamespace())
            ->toBe('com.laravelui5.core')
            ->and($dependencies)
            ->toBeArray()
            ->and(count($dependencies))
            ->toBe(1)
            ->and($dependencies[0])
            ->toBe('sap.ui.core');
    });
});
