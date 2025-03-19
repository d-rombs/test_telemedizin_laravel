<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $doctors = Doctor::with('specialization')->get();
        return response()->json($doctors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'specialization_id' => 'required|exists:specializations,id',
            ]);

            $doctor = Doctor::create($validatedData);
            return response()->json($doctor, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $doctor = Doctor::with('specialization')->findOrFail($id);
        return response()->json($doctor);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $doctor = Doctor::findOrFail($id);
            
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'specialization_id' => 'required|exists:specializations,id',
            ]);

            $doctor->update($validatedData);
            return response()->json($doctor);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->delete();
        
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Search for doctors by name or specialization.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('query');
        
        $doctors = Doctor::with('specialization')
            ->where('name', 'like', "%{$query}%")
            ->orWhereHas('specialization', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->get();
            
        return response()->json($doctors);
    }
}
