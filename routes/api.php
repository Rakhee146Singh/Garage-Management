<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\CarController;
use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\CityController;
use App\Http\Controllers\V1\UserController;
use App\Http\Controllers\V1\OrderController;
use App\Http\Controllers\V1\StateController;
use App\Http\Controllers\V1\StockController;
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
    /** Open API */
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('garage', [AuthController::class, 'garageList']);
    Route::post('stock', [AuthController::class, 'stockList']);
    Route::post('reset-password-email', [AuthController::class, 'resetMail']);
    Route::post('reset-password/{token}', [AuthController::class, 'reset']);

    Route::get('approve/{id}', [OrderController::class, 'approve']);
    Route::get('reject/{id}', [OrderController::class, 'reject']);
    // Route::get('invoice/{id}', [OrderController::class, 'invoice']);
    // Route::get('service/invoice/{id}', [CarServiceJobController::class, 'invoice']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::post('change-password', [AuthController::class, 'changePassword']);

        Route::group(['prefix' => 'admin', 'middleware' => 'services:admin'], function () {
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
            Route::controller(ServiceTypeController::class)->prefix('service')->group(function () {
                Route::post('/', 'list');
                Route::post('create', 'create');
                Route::get('show/{id}', 'show');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete');
            });
        });

        Route::group(['prefix' => 'owner', 'middleware' => 'services:owner'], function () {
            Route::controller(GarageController::class)->prefix('garage')->group(function () {
                Route::post('/', 'list');
                Route::post('create', 'create');
                Route::get('show/{id}', 'show');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete');
            });

            Route::controller(StockController::class)->prefix('stock')->group(function () {
                Route::post('/', 'list');
                Route::post('create', 'create');
                Route::get('show/{id}', 'show');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete');
            });

            Route::controller(OrderController::class)->prefix('order')->group(function () {
                Route::post('/', 'list');
                Route::post('create', 'create');
                Route::get('show/{id}', 'show');
                Route::get('invoice/{id}', 'invoice');
            });

            Route::controller(CarServiceController::class)->prefix('carservice')->group(function () {
                Route::post('status/{id}', 'status');
            });

            Route::controller(CarServiceJobController::class)->prefix('job')->group(function () {
                Route::post('create', 'create');
                Route::get('show/{id}', 'show');
                Route::post('update/{id}', 'update');
            });
        });

        Route::group(['prefix' => 'mechanic', 'middleware' => 'services:mechanic'], function () {
            Route::controller(CarServiceJobController::class)->prefix('job')->group(function () {
                Route::post('status/{id}', 'status');
            });
        });

        Route::group(['middleware' => 'services:owner|mechanic|customer'], function () {
            Route::controller(UserController::class)->group(function () {
                Route::post('/', 'list')->withoutMiddleware('services:customer');
                Route::post('create', 'create')->withoutMiddleware('services:customer|mechanic');
                Route::get('show', 'show');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete')->withoutMiddleware('services:customer|mechanic');
            });

            Route::controller(CarController::class)->prefix('car')->group(function () {
                Route::post('/', 'list');
                Route::post('create', 'create');
                Route::get('show/{id}', 'show');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete');
            });
            Route::controller(CarServiceJobController::class)->group(function () {
                Route::get('service/invoice/{id}', 'invoice')->withoutMiddleware('services:owner|mechanic');
            });
        });
    });
});
