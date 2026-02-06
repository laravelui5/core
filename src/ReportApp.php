<?php

namespace LaravelUi5\Core;

use Illuminate\Support\Facades\File;
use LaravelUi5\Core\Enums\ArtifactType;
use LaravelUi5\Core\Ui5\AbstractUi5App;
use LaravelUi5\Core\Ui5\Capabilities\LaravelUi5ManifestInterface;

class ReportApp extends AbstractUi5App
{
    public function getType(): ArtifactType
    {
        return ArtifactType::Application;
    }

    public function getNamespace(): string
    {
        return 'com.laravelui5.report';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }

    public function getTitle(): string
    {
        return 'Report';
    }

    public function getDescription(): string
    {
        return 'Generic Ui5 Report Application';
    }

    public function getUi5BootstrapAttributes(): array
    {
        return array (
  'theme' => 'sap_horizon',
  'oninit' => 'module:com/laravelui5/report/Component',
  'async' => 'true',
  'compatversion' => 'edge',
  'frameoptions' => 'trusted',
  'xx-waitfortheme' => 'true',
  'xx-supportedlanguages' => 'en,de',
);
    }

    public function getResourceNamespaces(): array
    {
        return [
        ];
    }

    public function getAdditionalHeadScript(): ?string
    {
        return <<<JS
sap.ui.getCore().attachInit(function () {
    sap.ui.core.Component.create({
      name: "com.laravelui5.report",
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
        return <<<CSS

CSS;
    }

    public function getAssetPath(string $filename): ?string
    {
        $path = __DIR__ . '/../resources/report-app/' . ltrim($filename, '/');
        return File::exists($path) ? $path : null;
    }

    public function getVendor(): string
    {
        return 'Pragmatiqu IT GmbH';
    }

    public function getManifestPath(): string
    {
        return __DIR__ . '/../resources/report-app/manifest.json';
    }

    public function getLaravelUiManifest(): LaravelUi5ManifestInterface
    {
        return app(CoreManifest::class);
    }
}
