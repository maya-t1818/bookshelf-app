<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\BookController as ApiBookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    Route::apiResource('books', ApiBookController::class);
});