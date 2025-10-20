<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;

class ServiceHandler
{
    public function getAll(): Collection
    {
        return Service::all();
    }

    public function getById(int $id): Service
    {
        return Service::findOrFail($id);
    }
}
