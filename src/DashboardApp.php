<?php

namespace LaravelUi5\Core;

use Illuminate\Support\Facades\File;
use LaravelUi5\Core\Attributes\HideIntent;
use LaravelUi5\Core\Enums\ArtifactType;
use LaravelUi5\Core\Ui5\Contracts\LaravelUi5ManifestInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5ModuleInterface;

#[HideIntent]
class DashboardApp implements Ui5AppInterface
{

    public function __construct(protected Ui5ModuleInterface $module)
    {
    }

    public function getModule(): Ui5ModuleInterface
    {
        return $this->module;
    }

    public function getType(): ArtifactType
    {
        return ArtifactType::Application;
    }

    public function getNamespace(): string
    {
        return 'com.laravelui5.dashboard';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }

    public function getTitle(): string
    {
        return 'Dashboard';
    }

    public function getDescription(): string
    {
        return 'Generic Ui5 Dashboard Application';
    }

    public function getUi5BootstrapAttributes(): array
    {
        return array(
            'theme' => 'sap_horizon',
            'oninit' => 'module:com/laravelui5/dashboard/Component',
            'async' => 'true',
            'compatversion' => 'edge',
            'frameoptions' => 'trusted',
            'xx-waitfortheme' => 'true',
            'xx-supportedlanguages' => 'en,de',
        );
    }

    public function getResourceNamespaces(): array
    {
        return array();
    }

    public function getAdditionalHeadScript(): ?string
    {
        return <<<JS
sap.ui.getCore().attachInit(function () {
    sap.ui.core.Component.create({
      name: "com.laravelui5.dashboard",
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
        $path = __DIR__ . '/../resources/dashboard-app/' . ltrim($filename, '/');
        return File::exists($path) ? $path : null;
    }

    public function getVendor(): string
    {
        return 'Pragmatiqu IT GmbH';
    }

    public function getManifestPath(): string
    {
        return __DIR__ . '/../resources/dashboard-app/manifest.json';
    }

    public function getLaravelUiManifest(): LaravelUi5ManifestInterface
    {
        return app(CoreManifest::class);
    }
}
