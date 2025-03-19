<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TimeSlot;
use App\Models\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class TimeSlotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $timeSlots = TimeSlot::with('doctor')->get();
        return response()->json($timeSlots);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'doctor_id' => 'required|exists:doctors,id',
                'start_time' => 'required|date|after:now',
                'end_time' => 'required|date|after:start_time',
                'is_available' => 'boolean',
            ]);

            $timeSlot = TimeSlot::create($validatedData);
            return response()->json($timeSlot, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $timeSlot = TimeSlot::with('doctor')->findOrFail($id);
        return response()->json($timeSlot);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $timeSlot = TimeSlot::findOrFail($id);
            
            $validatedData = $request->validate([
                'doctor_id' => 'exists:doctors,id',
                'start_time' => 'date',
                'end_time' => 'date|after:start_time',
                'is_available' => 'boolean',
            ]);

            $timeSlot->update($validatedData);
            return response()->json($timeSlot);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $timeSlot = TimeSlot::findOrFail($id);
        $timeSlot->delete();
        
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
    
    /**
     * Get available time slots for a specific doctor.
     */
    public function getAvailableByDoctor(string $doctorId): JsonResponse
    {
        $doctor = Doctor::findOrFail($doctorId);
        
        $timeSlots = TimeSlot::where('doctor_id', $doctorId)
            ->where('is_available', true)
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->get();
            
        return response()->json($timeSlots);
    }
    
    /**
     * Create multiple time slots for a doctor.
     */
    public function createMultiple(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'doctor_id' => 'required|exists:doctors,id',
                'date' => 'required|date|after:now',
                'start_hour' => 'required|integer|min:0|max:23',
                'end_hour' => 'required|integer|min:0|max:23|gt:start_hour',
                'slot_duration' => 'required|integer|min:15|max:120',
            ]);
            
            $doctorId = $validatedData['doctor_id'];
            $date = $validatedData['date'];
            $startHour = $validatedData['start_hour'];
            $endHour = $validatedData['end_hour'];
            $slotDuration = $validatedData['slot_duration']; // in minutes
            
            $createdSlots = [];
            $currentTime = strtotime($date . ' ' . $startHour . ':00:00');
            $endTime = strtotime($date . ' ' . $endHour . ':00:00');
            
            while ($currentTime < $endTime) {
                $slotStartTime = date('Y-m-d H:i:s', $currentTime);
                $slotEndTime = date('Y-m-d H:i:s', $currentTime + ($slotDuration * 60));
                
                $timeSlot = TimeSlot::create([
                    'doctor_id' => $doctorId,
                    'start_time' => $slotStartTime,
                    'end_time' => $slotEndTime,
                    'is_available' => true,
                ]);
                
                $createdSlots[] = $timeSlot;
                $currentTime += ($slotDuration * 60);
            }
            
            return response()->json($createdSlots, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    
    /**
     * Check real-time availability of a time slot.
     */
    public function checkAvailability(string $id): JsonResponse
    {
        $timeSlot = TimeSlot::findOrFail($id);
        
        return response()->json([
            'id' => $timeSlot->id,
            'is_available' => $timeSlot->is_available,
            'start_time' => $timeSlot->start_time,
            'end_time' => $timeSlot->end_time,
        ]);
    }
}
