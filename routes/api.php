<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Car\CarController;
use App\Http\Controllers\Car\FuelController;
use App\Http\Controllers\Car\TransmissionController;
use App\Http\Controllers\DriverController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/show/{id}', [CarController::class, 'show']);

Route::get('/report/sales', [AdminController::class, 'salesReport']);
Route::get('/report/search', [AdminController::class, 'reportSearch']);

Route::post('/transmission', [TransmissionController::class, 'store']);
Route::put('/transmission/{id}', [TransmissionController::class, 'update']);
Route::delete('/transmission/delete/{id}', [TransmissionController::class, 'destroy']);


Route::post('/drivers', [DriverController::class, 'store']);
Route::get('/drivers/edit', [DriverController::class, 'edit']);
