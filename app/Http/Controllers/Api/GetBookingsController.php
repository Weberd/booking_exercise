<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Services\BookingHandler;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GetBookingsController extends Controller
{
    public function __construct(private readonly BookingHandler $bookingHandler)
    {
    }

    public function __invoke(): AnonymousResourceCollection
    {
        return BookingResource::collection($this->bookingHandler->getAll());
    }
}
