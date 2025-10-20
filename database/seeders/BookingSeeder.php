<?php

namespace Database\Seeders;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $bookings = [
            // Поездка на квадроцикле 30 минут (ID: 1)
            ['service_id' => 1, 'date' => '2025-10-16', 'start' => '13:00', 'duration' => 60],
            ['service_id' => 1, 'date' => '2025-10-16', 'start' => '16:00', 'duration' => 60],
            ['service_id' => 1, 'date' => '2025-10-17', 'start' => '10:00', 'duration' => 60],
            ['service_id' => 1, 'date' => '2025-10-17', 'start' => '11:00', 'duration' => 60],
            ['service_id' => 1, 'date' => '2025-10-17', 'start' => '13:00', 'duration' => 60],
            ['service_id' => 1, 'date' => '2025-10-17', 'start' => '18:00', 'duration' => 60],

            // Поездка на квадроцикле 60 минут (ID: 2)
            ['service_id' => 2, 'date' => '2025-10-16', 'start' => '10:00', 'duration' => 90],

            // Тур на эндуро 60 минут (ID: 3)
            ['service_id' => 3, 'date' => '2025-10-16', 'start' => '10:00', 'duration' => 90],
            ['service_id' => 3, 'date' => '2025-10-16', 'start' => '11:30', 'duration' => 90],
            ['service_id' => 3, 'date' => '2025-10-16', 'start' => '18:30', 'duration' => 90],

            // Тур на эндуро 120 минут (ID: 4)
            ['service_id' => 4, 'date' => '2025-10-17', 'start' => '14:00', 'duration' => 150],
        ];

        foreach ($bookings as $booking) {
            $startTime = Carbon::parse($booking['start']);
            $endTime = $startTime->copy()->addMinutes($booking['duration']);

            Booking::create([
                'service_id' => $booking['service_id'],
                'customer_name' => 'Тестовый клиент',
                'customer_phone' => '+7 999 999-99-99',
                'booking_date' => $booking['date'],
                'start_time' => $startTime->format('H:i'),
                'end_time' => $endTime->format('H:i'),
                'status' => 'active',
            ]);
        }
    }
}
