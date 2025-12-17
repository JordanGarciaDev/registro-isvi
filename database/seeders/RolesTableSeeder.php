<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['name' => 'Administrador', 'guard_name' => 'web']);
        Role::create(['name' => 'Comercial', 'guard_name' => 'web']);
        Role::create(['name' => 'Turnos', 'guard_name' => 'web']);
    }
}
