<?php

namespace Database\Seeders;

use App\Models\Campus;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $campus = new Campus;
        $campus->Nombre = 'Medellin';
        $campus->Secreto = 'bc2e35e366c449918512fec22c1c7e28';
        $campus->save();

        $campus = new Campus;
        $campus->Nombre = 'Cali';
        $campus->Secreto = '4a52ba171a1545b285e81266afa9980d';
        $campus->Cola = "q2";
        $campus->save();

        $campus = new Campus;
        $campus->Nombre = 'Pereira';
        $campus->Secreto = '57dd0817e1974941a02da2d9e2f6f5d7';
        $campus->Cola = "q3";
        $campus->save();

        $campus = new Campus;
        $campus->Nombre = 'Barranquilla';
        $campus->Secreto = '43c78c07a67444b598ac0d6959914680';
        $campus->Cola = "q4";
        $campus->save();

        $campus = new Campus;
        $campus->Nombre = 'Bogota';
        $campus->Secreto = '25d890765d8b4a1c8efe74de44895fab';
        $campus->Cola = "q5";
        $campus->save();
    }
}
