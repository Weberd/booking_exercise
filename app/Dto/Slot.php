<?php

namespace App\Dto;

final readonly class Slot
{
    public function __construct(
        public string $time,
        public bool $available,
    ) {
    }
}
