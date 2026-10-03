<?php

namespace Database\Seeders;

use App\Models\Recipe\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'Quick',
            'Comfort Food',
            'Summer',
            'Family Friendly',
            'Budget',
            '15 Minutes',
            'One Pot',
        ];

        foreach ($tags as $name) {
            Tag::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name],
            );
        }
    }
}
