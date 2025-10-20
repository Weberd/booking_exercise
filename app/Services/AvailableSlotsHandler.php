<?php

namespace App\Services;

use App\Dto\Slot;
use App\Models\Booking;
use App\Models\Service;
use Carbon\Carbon;

final class AvailableSlotsHandler
{
    public function getAvailableSlots(int $serviceId, string $date): array
    {
        $service = Service::findOrFail($serviceId);
        $date = Carbon::parse($date);

        if ($date->dayOfWeek === Carbon::SUNDAY) {
            return [];
        }

        if ($date->isPast() && !$date->isToday()) {
            return [];
        }

        $slots = [];
        $workStart = Carbon::parse($date->format('Y-m-d') . ' 10:00');
        $workEnd = Carbon::parse($date->format('Y-m-d') . ' 20:00');

        $totalDuration = $service->duration;

        $current = $workStart->copy();

        while ($current->copy()->addMinutes($service->duration)->lte($workEnd)) {
            $slotStart = $current->format('H:i');
            $slotEnd = $current->copy()->addMinutes($totalDuration)->format('H:i');

            // Проверяем, свободен ли слот
            $isAvailable = !$this->hasConflict(
                $serviceId,
                $date->format('Y-m-d'),
                $slotStart,
                $slotEnd
            );

            $slots[] = new Slot($slotStart, $isAvailable);
            $current->addMinutes(30); // Шаг 30 минут
        }

        return $slots;
    }

    private function hasConflict(int $serviceId, string $date, string $startTime, string $endTime): bool
    {
        return Booking::where('service_id', $serviceId)
            ->where('booking_date', $date)
            ->where('status', 'active')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })
            ->exists();
    }
}
