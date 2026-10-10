<?php

namespace Database\Seeders;

use App\Models\Country\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['name' => 'England', 'code' => 'GB', 'slug' => 'england'],
            ['name' => 'America', 'code' => 'US', 'slug' => 'america'],
            ['name' => 'Mexico', 'code' => 'MX', 'slug' => 'mexico'],
            ['name' => 'Spain', 'code' => 'ES', 'slug' => 'spain'],
            ['name' => 'Italy', 'code' => 'IT', 'slug' => 'italy'],
            ['name' => 'Germany', 'code' => 'DE', 'slug' => 'germany'],
            ['name' => 'France', 'code' => 'FR', 'slug' => 'france'],
            ['name' => 'Greece', 'code' => 'GR', 'slug' => 'greece'],
        ];

        foreach ($countries as $country) {
            Country::query()->firstOrCreate(
                ['code' => $country['code']],
                [...$country, 'status' => 'active'],
            );
        }
    }
}
