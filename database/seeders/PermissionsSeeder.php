<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Permission;

class PermissionsSeeder extends Seeder
{

    /**
     * List of permissions to be added to the database.
     * 
     * @var array<string, string>
     */
    protected $permissions = [
        'user' => 'Can view, create, edit, and delete users',
        'campus' => 'Can view and export campus related information',
        'prosegur' => 'Can view and export Prosegur alarms',
        'horizon' => 'Can view the Horizon dashboard',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Loop through the permissions array and create a new Permission model
        // for each one.
        foreach ($this->permissions as $name => $description) {
            Permission::create([
                'name' => $name,
                'description' => $description,
                'gate' => 'manage:' . $name,
            ]);
        }
    }
}
