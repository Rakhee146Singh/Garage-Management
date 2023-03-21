<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\CarController;
use App\Http\Controllers\V1\CityController;
use App\Http\Controllers\V1\UserController;
use App\Http\Controllers\V1\StateController;
use App\Http\Controllers\V1\GarageController;
use App\Http\Controllers\V1\CountryController;
use App\Http\Controllers\V1\CarServiceController;
use App\Http\Controllers\V1\ServiceTypeController;
use App\Http\Controllers\V1\CarServiceJobController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::post('login', [UserController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('logout', [UserController::class, 'logout']);
        Route::controller(CountryController::class)->prefix('country')->group(function () {
            Route::post('/', 'list')->middleware('services:admin');
            Route::post('create', 'create')->middleware('services:admin');
            Route::get('show/{id}', 'show')->middleware('services:admin');
            Route::post('update/{id}', 'update')->middleware('services:admin');
            Route::post('delete/{id}', 'delete')->middleware('services:admin');
        });

        Route::controller(StateController::class)->prefix('state')->group(function () {
            Route::post('/', 'list')->middleware('services:admin');
            Route::post('create', 'create')->middleware('services:admin');
            Route::get('show/{id}', 'show')->middleware('services:admin');
            Route::post('update/{id}', 'update')->middleware('services:admin');
            Route::post('delete/{id}', 'delete')->middleware('services:admin');
        });

        Route::controller(CityController::class)->prefix('city')->group(function () {
            Route::post('/', 'list')->middleware('services:admin');
            Route::post('create', 'create')->middleware('services:admin');
            Route::get('show/{id}', 'show')->middleware('services:admin');
            Route::post('update/{id}', 'update')->middleware('services:admin');
            Route::post('delete/{id}', 'delete')->middleware('services:admin');
        });

        Route::controller(UserController::class)->prefix('user')->group(function () {
            Route::post('/', 'list')->middleware('services:admin|owner|mechanic');
            Route::post('create', 'create')->middleware('services:owner|mechanic|customer');
            Route::get('show/{id}', 'show')->middleware('services:owner|mechanic|customer');
            Route::post('update/{id}', 'update')->middleware('services:owner|mechanic|customer');
            Route::post('delete/{id}', 'delete')->middleware('services:admin|owner|mechanic');
        });

        Route::controller(GarageController::class)->prefix('garage')->group(function () {
            Route::post('/', 'list')->middleware('services:admin|owner');
            Route::post('create', 'create')->middleware('services:owner|mechanic');
            Route::get('show/{id}', 'show')->middleware('services:owner|mechanic');
            Route::post('update/{id}', 'update')->middleware('services:owner|mechanic');
            Route::post('delete/{id}', 'delete')->middleware('services:admin|owner|mechanic');
        });

        Route::controller(ServiceTypeController::class)->prefix('service')->group(function () {
            Route::post('/', 'list')->middleware('services:admin');
            Route::post('create', 'create')->middleware('services:admin');
            Route::get('show/{id}', 'show')->middleware('services:admin');
            Route::post('update/{id}', 'update')->middleware('services:admin');
            Route::post('delete/{id}', 'delete')->middleware('services:admin');
        });

        Route::controller(CarController::class)->prefix('car')->group(function () {
            Route::post('/', 'list')->middleware('services:admin|owner|mechanic|customer');
            Route::post('create', 'create')->middleware('services:owner|mechanic|customer');
            Route::get('show/{id}', 'show')->middleware('services:owner|mechanic|customer');
            Route::post('update/{id}', 'update')->middleware('services:owner|mechanic|customer');
            Route::post('delete/{id}', 'delete')->middleware('services:owner|mechanic|customer');
        });

        Route::controller(CarServiceController::class)->prefix('carservice')->group(function () {
            Route::post('/', 'list')->middleware('services:admin|owner|mechanic');
            Route::post('create', 'create')->middleware('services:owner|mechanic');
            Route::get('show/{id}', 'show')->middleware('services:owner|mechanic');
            Route::post('update/{id}', 'update')->middleware('services:owner|mechanic');
            Route::post('delete/{id}', 'delete')->middleware('services:owner|mechanic');
            Route::post('status/{id}', 'status')->middleware('services:owner|mechanic');
        });

        Route::controller(CarServiceJobController::class)->prefix('job')->group(function () {
            Route::post('/', 'list')->middleware('services:admin|owner|mechanic');
            Route::post('create', 'create')->middleware('services:owner|mechanic');
            Route::get('show/{id}', 'show')->middleware('services:owner|mechanic');
            Route::post('update/{id}', 'update')->middleware('services:owner|mechanic');
            Route::post('delete/{id}', 'delete')->middleware('services:owner|mechanic');
            Route::post('status/{id}', 'status')->middleware('services:owner|mechanic');
        });
    });
});
