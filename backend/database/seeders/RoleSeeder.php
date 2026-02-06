<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = ['owner', 'backend', 'frontend'];

        foreach ($names as $name) {
            Role::firstOrCreate(['name' => $name]);
        }
    }
}
