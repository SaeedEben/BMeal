<?php

namespace Database\Seeders;

use App\Models\User\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory()->create();

        $this->call(UnitSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(TagSeeder::class);

        $user = User::factory()->create([
            'full_name' => 'Saeed',
            'email' => 'saeed@gmail.com',
            'email_verified_at' => now(),
            'password' => 'password',
            'remember_token' => Str::random(10),
            'status' => 'active',
            'last_active' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Artisan::call('app:permissions');

        $this->call(RoleSeeder::class);

    }
}
