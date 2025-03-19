<?php

namespace Database\Seeders;

use App\Models\Specialization;
use Illuminate\Database\Seeder;

class SpecializationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specializations = [
            ['name' => 'Allgemeinmedizin'],
            ['name' => 'Kardiologie'],
            ['name' => 'Dermatologie'],
            ['name' => 'Neurologie'],
            ['name' => 'Orthopädie'],
            ['name' => 'Psychiatrie'],
            ['name' => 'Gynäkologie'],
            ['name' => 'Urologie'],
            ['name' => 'Pädiatrie'],
            ['name' => 'Augenheilkunde'],
        ];

        foreach ($specializations as $specialization) {
            Specialization::create($specialization);
        }
    }
}
