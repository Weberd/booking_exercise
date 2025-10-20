<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Поездка на квадроцикле (30 минут)', 'duration' => 30],
            ['name' => 'Поездка на квадроцикле (60 минут)', 'duration' => 60],
            ['name' => 'Тур на эндуро (60 минут)', 'duration' => 60],
            ['name' => 'Тур на эндуро (120 минут)', 'duration' => 120],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
