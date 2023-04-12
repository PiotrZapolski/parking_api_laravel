<?php

use App\Http\Controllers\Api\V1\ParkingController;
use App\Http\Controllers\Api\V1\VehicleController;
use App\Http\Controllers\Api\V1\ZoneController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth;

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

Route::post('auth/register', Auth\RegisterController::class)->name('api.auth.register');
Route::post('auth/login', Auth\LoginController::class)->name('api.auth.login');

Route::get('zones', [ZoneController::class, 'index'])->name('api.zones.index');

Route::middleware('auth:sanctum')->group(function() {
    Route::post('auth/logout', Auth\LogoutController::class)->name('api.auth.logout');

    Route::get('profile', [Auth\ProfileController::class, 'show'])->name('api.profile.show');
    Route::put('profile', [Auth\ProfileController::class, 'update'])->name('api.profile.update');
    Route::put('password', Auth\UpdatePasswordController::class)->name('api.profile.password.update');

    Route::apiResource('vehicles', VehicleController::class, ['as' => 'api']);

    Route::post('parkings/start', [ParkingController::class, 'start'])->name('api.parking.start');
    Route::get('parkings/{parking}', [ParkingController::class, 'show'])->name('api.parking.show');
    Route::put('parkings/{parking}/stop', [ParkingController::class, 'stop'])->name('api.parking.stop');
});
