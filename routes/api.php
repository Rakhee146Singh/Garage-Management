<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\CityController;
use App\Http\Controllers\V1\UserController;
use App\Http\Controllers\V1\StateController;
use App\Http\Controllers\V1\GarageController;
use App\Http\Controllers\V1\CountryController;
use App\Http\Controllers\V1\ServiceTypeController;

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

Route::prefix('V1')->group(function () {
    Route::post('login', [UserController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('logout', [UserController::class, 'logout']);
        Route::controller(CountryController::class)->prefix('country')->group(function () {
            Route::post('/', 'list');
            Route::post('create', 'create');
            Route::get('show/{id}', 'show');
            Route::post('update/{id}', 'update');
            Route::post('delete/{id}', 'delete');
        });

        Route::controller(StateController::class)->prefix('state')->group(function () {
            Route::post('/', 'list');
            Route::post('create', 'create');
            Route::get('show/{id}', 'show');
            Route::post('update/{id}', 'update');
            Route::post('delete/{id}', 'delete');
        });

        Route::controller(CityController::class)->prefix('city')->group(function () {
            Route::post('/', 'list');
            Route::post('create', 'create');
            Route::get('show/{id}', 'show');
            Route::post('update/{id}', 'update');
            Route::post('delete/{id}', 'delete');
        });

        Route::controller(UserController::class)->prefix('user')->group(function () {
            Route::post('/', 'list');
            Route::post('create', 'create');
            Route::get('show/{id}', 'show');
            Route::post('update/{id}', 'update');
            Route::post('delete/{id}', 'delete');
        });

        Route::controller(GarageController::class)->prefix('garage')->group(function () {
            Route::post('/', 'list');
            Route::post('create', 'create');
            Route::get('show/{id}', 'show');
            Route::post('update/{id}', 'update');
            Route::post('delete/{id}', 'delete');
        });

        Route::controller(ServiceTypeController::class)->prefix('service')->group(function () {
            Route::post('/', 'list');
            Route::post('create', 'create');
            Route::get('show/{id}', 'show');
            Route::post('update/{id}', 'update');
            Route::post('delete/{id}', 'delete');
        });
    });
});
