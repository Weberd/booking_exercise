<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Services\ServiceHandler;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GetServicesController extends Controller
{
    public function __construct(private readonly ServiceHandler $serviceHandler)
    {
    }

    public function __invoke(): AnonymousResourceCollection
    {
        return ServiceResource::collection($this->serviceHandler->getAll());
    }
}
