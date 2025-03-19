<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = [
            ['name' => 'Dr. Thomas Müller', 'specialization_id' => 1],
            ['name' => 'Dr. Anna Schmidt', 'specialization_id' => 2],
            ['name' => 'Dr. Michael Weber', 'specialization_id' => 3],
            ['name' => 'Dr. Julia Fischer', 'specialization_id' => 4],
            ['name' => 'Dr. Stefan Becker', 'specialization_id' => 5],
            ['name' => 'Dr. Laura Wagner', 'specialization_id' => 6],
            ['name' => 'Dr. Markus Hoffmann', 'specialization_id' => 7],
            ['name' => 'Dr. Sarah Schulz', 'specialization_id' => 8],
            ['name' => 'Dr. Daniel Meyer', 'specialization_id' => 9],
            ['name' => 'Dr. Lisa Schneider', 'specialization_id' => 10],
            ['name' => 'Dr. Tobias Koch', 'specialization_id' => 1],
            ['name' => 'Dr. Katharina Wolf', 'specialization_id' => 2],
            ['name' => 'Dr. Felix Bauer', 'specialization_id' => 3],
            ['name' => 'Dr. Nina Richter', 'specialization_id' => 4],
            ['name' => 'Dr. Philipp Schäfer', 'specialization_id' => 5],
        ];

        foreach ($doctors as $doctor) {
            Doctor::create($doctor);
        }
    }
}
