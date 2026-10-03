<?php

namespace Database\Seeders;

use App\Models\User\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::query()->firstOrCreate([
            'name' => 'customer',
            'guard_name' => 'web',
        ]);
    }
}
