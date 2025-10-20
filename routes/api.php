<?php

use App\Http\Controllers\Api\CreateBookingController;
use App\Http\Controllers\Api\GetAvailableSlotsController;
use App\Http\Controllers\Api\GetServicesController;
use Illuminate\Routing\Route;

Route::get('/api/v1/services', GetServicesController::class);
Route::get('/api/v1/services/{service}/slots/{date}', GetAvailableSlotsController::class);
Route::post('/api/v1/bookings', CreateBookingController::class);
