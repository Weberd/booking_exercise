<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class BookingIndexController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('booking/index');
    }
}
