<?php

use Illuminate\Support\Facades\Route;
use LaravelUi5\Core\Controllers\ODataController;

Route::any(
    '{namespace}',
    ODataController::class
)->where([
    'namespace' => '.+',
]);
