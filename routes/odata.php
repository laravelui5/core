<?php

use Illuminate\Support\Facades\Route;
use LaravelUi5\Core\Controllers\ODataController;

Route::any(
    '{namespace}@{version}/{path?}',
    ODataController::class
)->where([
    'namespace' => '.+',
    'version'   => '\d+\.\d+\.\d+',
    'path'      => '.*',
]);
