<?php

use Illuminate\Support\Facades\Route;
use LaravelUi5\Core\Controllers\AssetController;
use LaravelUi5\Core\Controllers\CardController;
use LaravelUi5\Core\Controllers\DashboardController;
use LaravelUi5\Core\Controllers\IndexController;
use LaravelUi5\Core\Controllers\ManifestController;
use LaravelUi5\Core\Controllers\ActionDispatchController;
use LaravelUi5\Core\Controllers\ReportActionDispatcher;
use LaravelUi5\Core\Controllers\ReportController;
use LaravelUi5\Core\Controllers\ReportResourceController;
use LaravelUi5\Core\Controllers\ResourceController;

/*
|--------------------------------------------------------------------------
| UI5 Core Routing
|--------------------------------------------------------------------------
|
| The following routes provide generic handlers for UI5 applications,
| libraries, dashboards, cards and actions. All routing is based on the
| module slug and artifact slug. Slugs are mapped to classes via the
| module and dashboard registry.
|
*/

/**
 * Route interception for dashboards and reports rendering
 */
Route::get('app/dashboard/{slug}/view/Dashboard.view.xml', DashboardController::class);
Route::get('app/report/{slug}/controller/Report.controller.{extension}', ReportResourceController::class);
Route::get('app/report/{slug}/view/Report.view.{extension}', ReportResourceController::class);


/**
 * REPORTACTION entrypoint (HTML|PDF|XSLX)
 */
Route::get('report/{slug}/{action}', ReportActionDispatcher::class);

/**
 * REPORT entrypoint (HTML|PDF|XSLX)
 */
Route::get('report/{slug}', ReportController::class);

/**
 * APPLICATION entrypoint (index.html)
 */
Route::get('app/{module}/{version}/index.html', IndexController::class);

/**
 * APPLICATION manifest.json
 */
Route::get('app/{module}/{version}/manifest.json', ManifestController::class);

/**
 * CARD manifest.json
 */
Route::get('card/{module}/{slug}/{version}/manifest.json', CardController::class);

/**
 * RESOURCE entrypoint
 */
Route::get('resource/{module}/{slug}/{version}', ResourceController::class);

/**
 * ACTION dispatcher (bound to app within a module)
 * Supports route-style parameters like /api/users/toggle-lock/{user}
 */
Route::match(['POST', 'PATCH', 'DELETE'], 'api/{module}/{segment}/{uri?}', ActionDispatchController::class)
    ->where('uri', '.*');

/**
 * ASSET delivery
 * Supports debug mode (-dbg.js), sourcemaps, fragments, etc.
 */
Route::get('{type}/{module}/{version}/{file}', AssetController::class)
    ->where([
        'type' => 'app|lib',
        'file' => '.*',
    ]);
