<?php

use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\DoctorController;
use App\Http\Controllers\API\SpecializationController;
use App\Http\Controllers\API\TimeSlotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Specialization routes
Route::apiResource('specializations', SpecializationController::class);

// Doctor routes
Route::get('doctors/search', [DoctorController::class, 'search']);
Route::apiResource('doctors', DoctorController::class);

// Appointment routes
Route::post('appointments/by-email', [AppointmentController::class, 'getByEmail']);
Route::put('appointments/{id}/cancel', [AppointmentController::class, 'cancel']);
Route::apiResource('appointments', AppointmentController::class);

// TimeSlot routes
Route::get('doctors/{doctorId}/time-slots', [TimeSlotController::class, 'getAvailableByDoctor']);
Route::post('time-slots/create-multiple', [TimeSlotController::class, 'createMultiple']);
Route::get('time-slots/{id}/check-availability', [TimeSlotController::class, 'checkAvailability']);
Route::apiResource('time-slots', TimeSlotController::class);
