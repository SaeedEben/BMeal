<?php

namespace Database\Seeders;

use App\Models\Recipe\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Vegetarian',
            'Vegan',
            'Healthy',
            'High Protein',
            'Low Carb',
            'Gluten Free',
        ];

        foreach ($categories as $name) {
            Category::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name],
            );
        }
    }
}
