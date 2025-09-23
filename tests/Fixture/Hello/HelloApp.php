<?php

namespace Tests\Fixture\Hello;

use Illuminate\Support\Facades\File;
use LaravelUi5\Core\Ui5\Contracts\LaravelUi5ManifestInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppInterface;
use LaravelUi5\Core\Enums\ArtifactType;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;

class HelloApp implements Ui5AppInterface
{

    public function __construct(protected Ui5ModuleInterface $module)
    {
    }

    public function getModule(): ?Ui5ModuleInterface
    {
        return $this->module;
    }

    public function getSlug(): string
    {
        return $this->module->getSlug();
    }

    public function getType(): ArtifactType
    {
        return ArtifactType::Application;
    }

    public function getNamespace(): string
    {
        return 'com.laravelui5.hello';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }

    public function getUrlKey(): string
    {
        return 'app/hello';
    }

    public function getTitle(): string
    {
        return 'Hello';
    }

    public function getDescription(): string
    {
        return 'Ui5App generated via ui5:sca';
    }

    public function getUi5BootstrapAttributes(): array
    {
        return array (
          'theme' => 'sap_horizon',
          'oninit' => 'module:com/laravelui5/hello/Component',
          'async' => 'true',
          'compatversion' => 'edge',
          'frameoptions' => 'trusted',
          'xx-waitfortheme' => 'true',
          'xx-supportedlanguages' => 'en',
        );
    }

    public function getResourceNamespaces(): array
    {
        return [
            'com.laravelui5.core'
        ];
    }

    public function getAdditionalHeadScript(): ?string
    {
        return <<<JS
sap.ui.getCore().attachInit(function () {
    sap.ui.core.Component.create({
      name: "com.laravelui5.hello",
      manifest: true,
      async: true
    }).then(function (oComponent) {
      new sap.ui.core.ComponentContainer({
        component: oComponent,
        height: "100%"
      }).placeAt("content");
    }).catch(function (err) {
      console.error("Component load failed:", err);
    });
});
JS;
    }

    public function getAdditionalInlineCss(): ?string
    {
        return null;
    }

    public function getSapUi5ManifestFragment(): string
    {
        return <<<JSON
{
    "rootView": {
        "viewName": "com.laravelui5.hello.view.App",
        "type": "XML",
        "async": true,
        "id": "app"
    },
    "dependencies": {
        "minUI5Version": "1.136.1",
        "libs": {
            "sap.ui.core": [],
            "sap.m": [],
            "com.laravelui5.core": []
        }
    },
    "handleValidation": true,
    "contentDensities": {
        "compact": true,
        "cozy": true
    },
    "models": {
        "i18n": {
            "type": "sap.ui.model.resource.ResourceModel",
            "settings": {
                "bundleName": "com.laravelui5.hello.i18n.i18n",
                "supportedLocales": [
                    "",
                    "de"
                ],
                "fallbackLocale": ""
            }
        }
    }
}
JSON;
    }

    public function getAssetPath(string $filename): ?string
    {
        $path = __DIR__ . '/../resources/app/' . ltrim($filename, '/');
        return File::exists($path) ? $path : null;
    }

    public function getManifestPath(): string
    {
        // TODO: Implement getManifestPath() method.
    }

    public function getLaravelUiManifest(): LaravelUi5ManifestInterface
    {
        // TODO: Implement getLaravelUiManifest() method.
    }

    public function getVendor(): string
    {
        return 'Pragmatiqu IT GmbH';
    }
}
