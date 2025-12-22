<?php

namespace LaravelUi5\Core\Controllers;

use Illuminate\Routing\Controller;
use LaravelUi5\Core\Contracts\Ui5ContextInterface;
use LaravelUi5\Core\Ui5\Contracts\Ui5AppInterface;
use LaravelUi5\Core\Ui5\Ui5Registry;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller
{
    public function __invoke(Ui5ContextInterface $context, Ui5Registry $registry, string $module, string $version): Response
    {
        /** @var Ui5AppInterface $app */
        $app = $context->artifact();

        $roots = array_merge(
            [$app->getNamespace() => './'],
            $registry->resolveRoots($app->getResourceNamespaces())
        );

        return response()->view('ui5::index', [
            'app' => $app,
            'roots' => $roots,
        ]);
    }
}
