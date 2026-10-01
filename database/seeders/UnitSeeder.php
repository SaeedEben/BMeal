<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Gram', 'symbol' => 'g', 'type' => 'weight', 'is_metric' => true],
            ['name' => 'Kilogram', 'symbol' => 'kg', 'type' => 'weight', 'is_metric' => true],
            ['name' => 'Milligram', 'symbol' => 'mg', 'type' => 'weight', 'is_metric' => true],
            ['name' => 'Ounce', 'symbol' => 'oz', 'type' => 'weight', 'is_metric' => false],
            ['name' => 'Pound', 'symbol' => 'lb', 'type' => 'weight', 'is_metric' => false],
            ['name' => 'Milliliter', 'symbol' => 'ml', 'type' => 'volume', 'is_metric' => true],
            ['name' => 'Liter', 'symbol' => 'l', 'type' => 'volume', 'is_metric' => true],
            ['name' => 'Teaspoon', 'symbol' => 'tsp', 'type' => 'volume', 'is_metric' => false],
            ['name' => 'Tablespoon', 'symbol' => 'tbsp', 'type' => 'volume', 'is_metric' => false],
            ['name' => 'Cup', 'symbol' => 'cup', 'type' => 'volume', 'is_metric' => false],
            ['name' => 'Fluid Ounce', 'symbol' => 'fl oz', 'type' => 'volume', 'is_metric' => false],
            ['name' => 'Pint', 'symbol' => 'pt', 'type' => 'volume', 'is_metric' => false],
            ['name' => 'Quart', 'symbol' => 'qt', 'type' => 'volume', 'is_metric' => false],
            ['name' => 'Gallon', 'symbol' => 'gal', 'type' => 'volume', 'is_metric' => false],
            ['name' => 'Piece', 'symbol' => 'pcs', 'type' => 'count', 'is_metric' => false],
            ['name' => 'Clove', 'symbol' => 'clove', 'type' => 'count', 'is_metric' => false],
            ['name' => 'Slice', 'symbol' => 'slice', 'type' => 'count', 'is_metric' => false],
            ['name' => 'Can', 'symbol' => 'can', 'type' => 'count', 'is_metric' => false],
            ['name' => 'Package', 'symbol' => 'pkg', 'type' => 'count', 'is_metric' => false],
            ['name' => 'Bottle', 'symbol' => 'bottle', 'type' => 'count', 'is_metric' => false],
            ['name' => 'Bunch', 'symbol' => 'bunch', 'type' => 'count', 'is_metric' => false],
            ['name' => 'Head', 'symbol' => 'head', 'type' => 'count', 'is_metric' => false],
            ['name' => 'Stick', 'symbol' => 'stick', 'type' => 'count', 'is_metric' => false],
            ['name' => 'Fillet', 'symbol' => 'fillet', 'type' => 'count', 'is_metric' => false],
        ];

        $now = now();
        $units = array_map(static function (array $unit) use ($now): array {
            return [
                'id' => (string) Str::uuid(),
                'name' => $unit['name'],
                'symbol' => $unit['symbol'],
                'slug' => Str::slug($unit['name']),
                'type' => $unit['type'],
                'is_metric' => $unit['is_metric'],
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $units);

        DB::table('units')->upsert(
            $units,
            ['slug'],
            ['name', 'symbol', 'type', 'is_metric', 'status', 'updated_at'],
        );
    }
}
