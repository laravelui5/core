<?php

use Illuminate\Support\Facades\Route;
use LaravelUi5\Core\Controllers\AssetController;
use LaravelUi5\Core\Controllers\CardController;
use LaravelUi5\Core\Controllers\DashboardController;
use LaravelUi5\Core\Controllers\IndexController;
use LaravelUi5\Core\Controllers\ManifestController;
use LaravelUi5\Core\Controllers\ActionDispatchController;
use LaravelUi5\Core\Controllers\ReportController;
use LaravelUi5\Core\Controllers\ReportResourceController;
use LaravelUi5\Core\Controllers\ResourceController;

/*
|--------------------------------------------------------------------------
| LaravelUi5 Core Routing — v3.0
|--------------------------------------------------------------------------
|
| Canonical routing model:
|   {type}/{namespace}@{version}/...
|
| - namespace may contain slashes
| - version is explicit and immutable
| - identity is namespace@version
|
*/

/**
 * Route interception for dashboards and reports rendering
 */
Route::get(
    'app/com/laravelui5/dashboard/{namespace}@{version}/view/Dashboard.view.xml',
    DashboardController::class
)->where([
    'namespace' => '.+',
    'version'   => '\d+\.\d+\.\d+',
]);
Route::get(
    'app/com/laravelui5/report/{namespace}@{version}/controller/Report.controller.{extension}',
    ReportResourceController::class
)->where([
    'namespace' => '.+',
    'version'   => '\d+\.\d+\.\d+',
]);
Route::get(
    'app/com/laravelui5/report/{namespace}@{version}/view/Report.view.{extension}',
    ReportResourceController::class
)->where([
    'namespace' => '.+',
    'version'   => '\d+\.\d+\.\d+',
]);

/**
 * REPORTACTION entrypoint (HTML|PDF|XSLX)
 * TODO Umbauen auf generische Action!
 */
//Route::post('report/{slug}/{action}', ReportActionDispatchController::class);

/**
 * REPORT entrypoint (HTML|PDF|XSLX)
 */
Route::get('report/{namespace}@{version}', ReportController::class)
    ->where([
        'namespace' => '.+',
        'version'   => '\d+\.\d+\.\d+',
    ]);

/**
 * APPLICATION entrypoint (index.html)
 */
Route::get('app/{namespace}@{version}/index.html', IndexController::class)
    ->where([
        'namespace' => '.+',
        'version'   => '\d+\.\d+\.\d+',
    ])->name('ui5.app');

/**
 * APPLICATION manifest.json
 */
Route::get('app/{namespace}@{version}/manifest.json', ManifestController::class)
    ->where([
        'namespace' => '.+',
        'version'   => '\d+\.\d+\.\d+',
    ]);

/**
 * CARD manifest.json
 */
Route::get('card/{namespace}@{version}/manifest.json', CardController::class)
    ->where([
        'namespace' => '.+',
        'version'   => '\d+\.\d+\.\d+',
    ]);

/**
 * RESOURCE entrypoint
 */
Route::get('resource/{namespace}@{version}', ResourceController::class)
    ->where([
        'namespace' => '.+',
        'version'   => '\d+\.\d+\.\d+',
    ]);

/**
 * ACTION dispatcher (bound to app within a module)
 * Supports route-style parameters like /api/users/toggle-lock/{user}
 */
Route::match(['POST', 'PATCH', 'DELETE'],
    'api/{namespace}@{version}/{uri?}',
    ActionDispatchController::class
)->where([
    'namespace' => '.+',
    'version'   => '\d+\.\d+\.\d+',
    'uri'       => '.*',
]);

/**
 * ASSET delivery
 * Supports debug mode (-dbg.js), sourcemaps, fragments, etc.
 */
Route::get('{type}/{namespace}@{version}/{file}', AssetController::class)
    ->where([
        'type' => 'app|lib',
        'namespace' => '.+',
        'version'   => '\d+\.\d+\.\d+',
        'file' => '.*',
    ]);
