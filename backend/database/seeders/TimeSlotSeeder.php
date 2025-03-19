<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all doctors
        $doctors = Doctor::all();
        
        // Create time slots for the next 7 days
        for ($day = 0; $day < 7; $day++) {
            $date = Carbon::now()->addDays($day)->format('Y-m-d');
            
            foreach ($doctors as $doctor) {
                // Morning slots (9:00 - 12:00)
                for ($hour = 9; $hour < 12; $hour++) {
                    $startTime = Carbon::parse("$date $hour:00:00");
                    $endTime = Carbon::parse("$date $hour:30:00");
                    
                    TimeSlot::create([
                        'doctor_id' => $doctor->id,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'is_available' => true,
                    ]);
                    
                    $startTime = Carbon::parse("$date $hour:30:00");
                    $endTime = Carbon::parse("$date " . ($hour + 1) . ":00:00");
                    
                    TimeSlot::create([
                        'doctor_id' => $doctor->id,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'is_available' => true,
                    ]);
                }
                
                // Afternoon slots (14:00 - 17:00)
                for ($hour = 14; $hour < 17; $hour++) {
                    $startTime = Carbon::parse("$date $hour:00:00");
                    $endTime = Carbon::parse("$date $hour:30:00");
                    
                    TimeSlot::create([
                        'doctor_id' => $doctor->id,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'is_available' => true,
                    ]);
                    
                    $startTime = Carbon::parse("$date $hour:30:00");
                    $endTime = Carbon::parse("$date " . ($hour + 1) . ":00:00");
                    
                    TimeSlot::create([
                        'doctor_id' => $doctor->id,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'is_available' => true,
                    ]);
                }
            }
        }
    }
}
