<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Q10\Models\Campus;

class CampusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $campus = config('Q10.campus');
        // Loop through the campus array and create a new Campus model
        // for each one.
        foreach ($campus as $name => $api_key) {
            Campus::create([
                'Nombre' => $name,
                'Secreto' => $api_key,
            ]);
        }
    }
}
