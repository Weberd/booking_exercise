<?php

namespace App\Http\Controllers\Api;

use App\Dto\BookingRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBookingRequest;
use App\Services\CreateBookingCommand;
use App\Services\ServiceHandler;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class CreateBookingController extends Controller
{
    public function __construct(
        private readonly CreateBookingCommand $createBookingCommand,
        private readonly ServiceHandler $serviceHandler,
    ) {
    }

    public function __invoke(CreateBookingRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $service = Service::findOrFail($data['service_id']);
            $totalDuration = $service->duration + 30;

            $startTime = Carbon::parse($data['start_time']);
            $endTime = $startTime->copy()->addMinutes($totalDuration);

            $data['end_time'] = $endTime->format('H:i');

            $bookingRequest = new BookingRequest(
                $data['service_id'],
                $data['customer_name'],
                $data['customer_phone'],
                $data['booking_date'],
                $data['start_time'],
                $data['end_time'],
            );

            $booking = $this->createBookingCommand->create($bookingRequest);

            return response()->json([
                'success' => true,
                'message' => 'Бронирование успешно создано',
                'booking' => $booking,
            ], 201);

        } catch (BookingConflictException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 409);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
