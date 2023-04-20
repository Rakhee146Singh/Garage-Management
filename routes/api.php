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
/* ONLY 40 percent work are satisfied */


/* Code explanation is missing in whole project */

Route::prefix('v1')->group(function () {
    /** Open API */
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('garage', [AuthController::class, 'garageList']);
    Route::post('stock', [AuthController::class, 'stockList']);
    Route::post('reset-password-email', [AuthController::class, 'resetMail']);
    Route::post('reset-password/{token}', [AuthController::class, 'reset']);

<<<<<<< HEAD
    /* Registration APIs for owner/garage, customer are missing */
=======
    Route::get('approve/{id}', [OrderController::class, 'approve']);
    Route::get('reject/{id}', [OrderController::class, 'reject']);
    // Route::get('invoice/{id}', [OrderController::class, 'invoice']);
    // Route::get('service/invoice/{id}', [CarServiceJobController::class, 'invoice']);
>>>>>>> f22399f537600bf34ad5e7b6cdb932e3f638c3c2

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::post('change-password', [AuthController::class, 'changePassword']);

<<<<<<< HEAD
        Route::group(['prefix' => 'admin','middleware' => 'services:admin'], function () {
=======
        Route::group(['prefix' => 'admin', 'middleware' => 'services:admin'], function () {
>>>>>>> f22399f537600bf34ad5e7b6cdb932e3f638c3c2
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

<<<<<<< HEAD
        /* Whole "services" middleware naming convention is wrong */
        Route::group(['prefix' => 'owner'], function () {

            /* Owner update profile missing */
            /* Owner update garage information missing */
            /* Garage > list of mechanics API missing */
            /* Garage > view garage information including what services are working on */
            /* Garage > Add mechanics API missing */
            /* Garage > update mechanics API missing */
            /* Garage > remove mechanics API missing */
            /* Garage > add service API missing */
            /* Garage > remove service API missing */

            /* Whole user group API are wrong */
            Route::controller(UserController::class)->prefix('user')->group(function () {
                Route::post('/', 'list')->middleware('services:admin|owner|mechanic'); // wrong
                Route::post('create', 'create')->middleware('services:admin|owner|mechanic|customer'); // wrong
                Route::get('show/{id}', 'show')->middleware('services:owner|mechanic|customer'); //wrong
                Route::post('update/{id}', 'update')->middleware('services:owner|mechanic|customer'); //wrong
                Route::post('delete/{id}', 'delete')->middleware('services:admin|owner|mechanic'); //wrong
            });

            Route::controller(GarageController::class)->prefix('garage')->group(function () {
                Route::post('/', 'list')->middleware('services:admin|owner|mechanic|customer');
                Route::post('create', 'create')->middleware('services:owner|mechanic'); // No meaning of this API
                Route::get('show/{id}', 'show')->middleware('services:owner|mechanic'); // wrong services/middleware for mechanic
                Route::post('update/{id}', 'update')->middleware('services:owner|mechanic'); // Mechanic not able to update the garage.
                Route::post('delete/{id}', 'delete')->middleware('services:admin|owner|mechanic');
            });

            Route::controller(CarServiceController::class)->prefix('carservice')->group(function () {
                /* owner can see list of assign/added cars API missing */
                /* owner can see assign/added car detail API missing */
                Route::post('status/{id}', 'status')->middleware('services:owner|mechanic');
=======
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
>>>>>>> f22399f537600bf34ad5e7b6cdb932e3f638c3c2
            });

            Route::controller(CarServiceJobController::class)->prefix('job')->group(function () {
                Route::post('create', 'create');
                Route::get('show/{id}', 'show');
                Route::post('update/{id}', 'update');
            });
        });

<<<<<<< HEAD
        Route::group(['prefix' => 'customer'], function () {
            /* customer update profile missing */
            /* customer add cars API is there but not a fluent */
            /* customer remove cars API */
            /* customer > list of own added cars */
            /* customer > car details */
            /* customer > track car service status */
=======
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
>>>>>>> f22399f537600bf34ad5e7b6cdb932e3f638c3c2

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
