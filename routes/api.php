<?php

use App\Http\Controllers\API\DriverController;
use App\Http\Controllers\API\AddressController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('driver', [DriverController::class, 'all']);
Route::get('driver/report', [DriverController::class, 'getAnswer']);
Route::post('address/check', [AddressController::class, 'submitAddress']);
