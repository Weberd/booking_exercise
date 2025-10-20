<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service' => $this->service,
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'booking_date' => $this->booking_date?->toISOString(),
            'start_time' => $this->start_time?->toISOString(),
            'end_time' => $this->end_time?->toISOString(),
            'status' => $this->status,
        ];
    }
}
