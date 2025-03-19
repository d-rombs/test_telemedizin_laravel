<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\TimeSlot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $appointments = Appointment::with('doctor.specialization')->get();
        return response()->json($appointments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'doctor_id' => 'required|exists:doctors,id',
                'patient_name' => 'required|string|max:255',
                'patient_email' => 'required|email|max:255',
                'date_time' => 'required|date|after:now',
                'time_slot_id' => 'required|exists:time_slots,id',
            ]);

            // Check if the time slot is available
            $timeSlot = TimeSlot::findOrFail($validatedData['time_slot_id']);
            
            if (!$timeSlot->is_available) {
                return response()->json([
                    'message' => 'Der gewählte Zeitslot ist nicht mehr verfügbar.'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Create appointment
            $appointment = Appointment::create([
                'doctor_id' => $validatedData['doctor_id'],
                'patient_name' => $validatedData['patient_name'],
                'patient_email' => $validatedData['patient_email'],
                'date_time' => $validatedData['date_time'],
                'status' => 'scheduled'
            ]);

            // Mark time slot as unavailable
            $timeSlot->update(['is_available' => false]);

            // Send confirmation email (simulated for this task)
            $this->sendConfirmationEmail($appointment);

            return response()->json($appointment, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $appointment = Appointment::with('doctor.specialization')->findOrFail($id);
        return response()->json($appointment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $appointment = Appointment::findOrFail($id);
            
            $validatedData = $request->validate([
                'status' => 'required|in:scheduled,completed,cancelled',
            ]);

            $appointment->update($validatedData);
            return response()->json($appointment);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);
        
        // If the appointment is cancelled, make the time slot available again
        if ($appointment->status === 'scheduled') {
            // Find the time slot that matches this appointment's date_time
            $timeSlot = TimeSlot::where('doctor_id', $appointment->doctor_id)
                ->where('start_time', '<=', $appointment->date_time)
                ->where('end_time', '>=', $appointment->date_time)
                ->first();
                
            if ($timeSlot) {
                $timeSlot->update(['is_available' => true]);
            }
        }
        
        $appointment->delete();
        
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
    
    /**
     * Get appointments by patient email.
     */
    public function getByEmail(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|email',
            ]);
            
            $appointments = Appointment::with('doctor.specialization')
                ->where('patient_email', $validatedData['email'])
                ->get();
                
            return response()->json($appointments);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    
    /**
     * Cancel an appointment.
     */
    public function cancel(string $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);
        
        if ($appointment->status !== 'scheduled') {
            return response()->json([
                'message' => 'Nur geplante Termine können storniert werden.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        $appointment->update(['status' => 'cancelled']);
        
        // Make the time slot available again
        $timeSlot = TimeSlot::where('doctor_id', $appointment->doctor_id)
            ->where('start_time', '<=', $appointment->date_time)
            ->where('end_time', '>=', $appointment->date_time)
            ->first();
            
        if ($timeSlot) {
            $timeSlot->update(['is_available' => true]);
        }
        
        return response()->json($appointment);
    }
    
    /**
     * Send confirmation email (simulated)
     */
    private function sendConfirmationEmail(Appointment $appointment): void
    {
        \Log::info('Terminbestätigung gesendet an: ' . $appointment->patient_email);
    }
}
