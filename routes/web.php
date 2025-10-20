<?php

use App\Http\Controllers\Web\BookingIndexController;
use Illuminate\Support\Facades\Route;

Route::get('/', BookingIndexController::class);
