<?php

use App\Http\Controllers\Api\CreateBookingController;
use App\Http\Controllers\Api\GetAvailableSlotsController;
use App\Http\Controllers\Api\GetBookingsController;
use App\Http\Controllers\Api\GetServicesController;
use Illuminate\Support\Facades\Route;

Route::get('/v1/services', GetServicesController::class);
Route::get('/v1/services/{service}/slots/{date}', GetAvailableSlotsController::class);
Route::get('/v1/bookings', GetBookingsController::class);
Route::post('/v1/bookings', CreateBookingController::class);
