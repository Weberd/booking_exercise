<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SlotResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'time' => $this->time,
            'available' => $this->available,
        ];
    }
}
