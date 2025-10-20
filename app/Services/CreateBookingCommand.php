<?php

namespace App\Services;

use App\Dto\BookingRequest;
use App\Exception\BookingConflictException;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final class CreateBookingCommand
{
    private function timesOverlap(string $start1, string $end1, string $start2, string $end2): bool
    {
        return ($start1 < $end2) && ($end1 > $start2);
    }

    public function create(BookingRequest $bookingRequest): Booking
    {
        $startHour = (int) explode(':', $bookingRequest->startTime)[0];
        if ($startHour < 10 || $startHour >= 20) {
            throw new \InvalidArgumentException('Бронирование возможно только с 10:00 до 20:00');
        }

        $date = Carbon::parse($bookingRequest->date);
        if ($date->dayOfWeek === Carbon::SUNDAY) {
            throw new \InvalidArgumentException('По воскресеньям бронирование невозможно');
        }

        return DB::transaction(function () use ($bookingRequest) {
            $conflictingBookings = Booking::where('service_id', $bookingRequest->serviceId)
                ->where('booking_date', $bookingRequest->date)
                ->where('status', 'active')
                ->lockForUpdate()
                ->get();

            // Проверяем пересечение времени
            foreach ($conflictingBookings as $booking) {
                if ($this->timesOverlap(
                    $booking->start_time,
                    $booking->end_time,
                    $bookingRequest->startTime,
                    $bookingRequest->endTime
                )) {
                    throw new BookingConflictException('Время уже занято другим бронированием');
                }
            }

            // Создаем бронирование
            return Booking::create([
                'service_id' => $bookingRequest->serviceId,
                'customer_name' => $bookingRequest->customerName,
                'customer_phone' => $bookingRequest->customerPhone,
                'booking_date' => $bookingRequest->date,
                'start_time' => $bookingRequest->startTime,
                'end_time' => $bookingRequest->endTime,
            ]);
        });
    }
}
