<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetAvailableSlotsRequest;
use App\Http\Resources\SlotResource;
use App\Services\AvailableSlotsHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GetAvailableSlotsController extends Controller
{
    public function __construct(private readonly AvailableSlotsHandler $availableSlotsHandler)
    {
    }

    public function __invoke(GetAvailableSlotsRequest $request): JsonResponse
    {
        $data = $request->validated();
        $slots = SlotResource::collection($this->availableSlotsHandler->getAvailableSlots($data['service'], $data['date']));
        return response()->json($slots);
    }
}
