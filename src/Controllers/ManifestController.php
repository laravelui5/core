<?php

namespace LaravelUi5\Core\Controllers;

use Flat3\Lodata\Interfaces\ServiceEndpointInterface;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use LaravelUi5\Core\Contracts\Ui5CoreContext;
use LaravelUi5\Core\Exceptions\MissingManifestException;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppInterface;
use Symfony\Component\HttpFoundation\Response;

class ManifestController extends Controller
{
    public function __invoke(Ui5CoreContext $context, string $slug, string $version): Response
    {
        /** @var Ui5AppInterface $app */
        $app = $context->artifact;

        $manifestPath = $app->getManifestPath();
        if (!File::exists($manifestPath)) {
            throw new MissingManifestException($manifestPath);
        }

        $manifest = json_decode(File::get($manifestPath), true);

        if ($app instanceof ServiceEndpointInterface) {
            $manifest['sap.app']['dataSources'] = [
                'mainService' => [
                    'uri' => $app->endpoint(),
                    'type' => 'OData',
                    'settings' => [
                        'odataVersion' => '4.0'
                    ]
                ]
            ];
            $manifest['sap.ui5']['models'] = [
                '' => [
                    'dataSource' => 'mainService',
                    'settings' => [
                        'operationMode' => 'Server'
                    ]
                ]
            ];
        }

        $manifest['laravel.ui5'] = $app->getLaravelUiManifest()->getFragment($slug);

        return response()->json(
            $this->emptyArraysToObjects($manifest),
            200,
            [],
            JSON_UNESCAPED_SLASHES
        );
    }

    private function emptyArraysToObjects(array $input): array
    {
        foreach ($input as $key => &$value) {
            if (is_array($value)) {
                $value = empty($value)
                    ? new \stdClass()
                    : $this->emptyArraysToObjects($value);
            }
        }
        return $input;
    }
}
