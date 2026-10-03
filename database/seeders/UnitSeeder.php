<?php

namespace Database\Seeders;

use App\Enum\Recipe\UnitTypeEnum;
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
            ['name' => 'Gram', 'symbol' => 'g', 'type' => UnitTypeEnum::WEIGHT, 'is_metric' => true],
            ['name' => 'Kilogram', 'symbol' => 'kg', 'type' => UnitTypeEnum::WEIGHT, 'is_metric' => true],
            ['name' => 'Milligram', 'symbol' => 'mg', 'type' => UnitTypeEnum::WEIGHT, 'is_metric' => true],
            ['name' => 'Ounce', 'symbol' => 'oz', 'type' => UnitTypeEnum::WEIGHT, 'is_metric' => false],
            ['name' => 'Pound', 'symbol' => 'lb', 'type' => UnitTypeEnum::WEIGHT, 'is_metric' => false],
            ['name' => 'Milliliter', 'symbol' => 'ml', 'type' => UnitTypeEnum::VOLUME, 'is_metric' => true],
            ['name' => 'Liter', 'symbol' => 'l', 'type' => UnitTypeEnum::VOLUME, 'is_metric' => true],
            ['name' => 'Teaspoon', 'symbol' => 'tsp', 'type' => UnitTypeEnum::VOLUME, 'is_metric' => false],
            ['name' => 'Tablespoon', 'symbol' => 'tbsp', 'type' => UnitTypeEnum::VOLUME, 'is_metric' => false],
            ['name' => 'Cup', 'symbol' => 'cup', 'type' => UnitTypeEnum::VOLUME, 'is_metric' => false],
            ['name' => 'Fluid Ounce', 'symbol' => 'fl oz', 'type' => UnitTypeEnum::VOLUME, 'is_metric' => false],
            ['name' => 'Pint', 'symbol' => 'pt', 'type' => UnitTypeEnum::VOLUME, 'is_metric' => false],
            ['name' => 'Quart', 'symbol' => 'qt', 'type' => UnitTypeEnum::VOLUME, 'is_metric' => false],
            ['name' => 'Gallon', 'symbol' => 'gal', 'type' => UnitTypeEnum::VOLUME, 'is_metric' => false],
            ['name' => 'Piece', 'symbol' => 'pcs', 'type' => UnitTypeEnum::COUNT, 'is_metric' => false],
            ['name' => 'Clove', 'symbol' => 'clove', 'type' => UnitTypeEnum::COUNT, 'is_metric' => false],
            ['name' => 'Slice', 'symbol' => 'slice', 'type' => UnitTypeEnum::COUNT, 'is_metric' => false],
            ['name' => 'Can', 'symbol' => 'can', 'type' => UnitTypeEnum::COUNT, 'is_metric' => false],
            ['name' => 'Package', 'symbol' => 'pkg', 'type' => UnitTypeEnum::COUNT, 'is_metric' => false],
            ['name' => 'Bottle', 'symbol' => 'bottle', 'type' => UnitTypeEnum::COUNT, 'is_metric' => false],
            ['name' => 'Bunch', 'symbol' => 'bunch', 'type' => UnitTypeEnum::COUNT, 'is_metric' => false],
            ['name' => 'Head', 'symbol' => 'head', 'type' => UnitTypeEnum::COUNT, 'is_metric' => false],
            ['name' => 'Stick', 'symbol' => 'stick', 'type' => UnitTypeEnum::COUNT, 'is_metric' => false],
            ['name' => 'Fillet', 'symbol' => 'fillet', 'type' => UnitTypeEnum::COUNT, 'is_metric' => false],
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
