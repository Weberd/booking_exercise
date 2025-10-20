<?php

namespace App\Dto;

readonly final class BookingRequest
{
    public function __construct(
        public int    $serviceId,
        public string $customerName,
        public string $customerPhone,
        public string $date,
        public string $startTime,
        public string $endTime,
    )
    {
    }
}
