<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Collection;

class BookingHandler
{
        public function getAll(): Collection
        {
            return Booking::all();
        }
}
