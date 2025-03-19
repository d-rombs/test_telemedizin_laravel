<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Specialization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class SpecializationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $specializations = Specialization::all();
        return response()->json($specializations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:specializations',
            ]);

            $specialization = Specialization::create($validatedData);
            return response()->json($specialization, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $specialization = Specialization::findOrFail($id);
        return response()->json($specialization);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $specialization = Specialization::findOrFail($id);
            
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:specializations,name,' . $id,
            ]);

            $specialization->update($validatedData);
            return response()->json($specialization);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $specialization = Specialization::findOrFail($id);
        $specialization->delete();
        
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
