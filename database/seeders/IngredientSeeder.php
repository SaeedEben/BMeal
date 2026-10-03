<?php

namespace Database\Seeders;

use App\Models\Recipe\Ingredient;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredients = [
            ['name' => 'Chicken Breast', 'slug' => 'chicken-breast', 'description' => 'Boneless and skinless chicken breast, commonly used in salads, grilled dishes, and main courses.'],
            ['name' => 'Beef', 'slug' => 'beef', 'description' => 'Beef meat used in steaks, stews, burgers, and various international dishes.'],
            ['name' => 'Salmon', 'slug' => 'salmon', 'description' => 'Fresh salmon fillet commonly used in grilled, baked, and pan-seared dishes.'],
            ['name' => 'Egg', 'slug' => 'egg', 'description' => 'Chicken eggs used in breakfasts, baking, sauces, and many other recipes.'],
            ['name' => 'Rice', 'slug' => 'rice', 'description' => 'A staple grain used in dishes ranging from Asian fried rice to Mediterranean and Middle Eastern meals.'],
            ['name' => 'Pasta', 'slug' => 'pasta', 'description' => 'Wheat-based pasta commonly used in Italian and international dishes.'],
            ['name' => 'Tomato', 'slug' => 'tomato', 'description' => 'Fresh tomatoes used in salads, sauces, soups, and many Mediterranean dishes.'],
            ['name' => 'Onion', 'slug' => 'onion', 'description' => 'A versatile vegetable used as a base ingredient in soups, sauces, stews, and many savory dishes.'],
            ['name' => 'Garlic', 'slug' => 'garlic', 'description' => 'Aromatic bulb used to add flavor to sauces, marinades, soups, and savory dishes.'],
            ['name' => 'Potato', 'slug' => 'potato', 'description' => 'A starchy root vegetable used in fries, soups, stews, mashed potatoes, and roasted dishes.'],
            ['name' => 'Carrot', 'slug' => 'carrot', 'description' => 'A root vegetable used in salads, soups, stews, and roasted dishes.'],
            ['name' => 'Bell Pepper', 'slug' => 'bell-pepper', 'description' => 'A sweet pepper commonly used in salads, stir-fries, stews, and grilled dishes.'],
            ['name' => 'Spinach', 'slug' => 'spinach', 'description' => 'Leafy green vegetable used in salads, soups, pasta, omelets, and healthy meals.'],
            ['name' => 'Mozzarella Cheese', 'slug' => 'mozzarella-cheese', 'description' => 'Soft Italian cheese commonly used on pizza, pasta, salads, and baked dishes.'],
            ['name' => 'Olive Oil', 'slug' => 'olive-oil', 'description' => 'Vegetable oil widely used for cooking, frying, salad dressings, and Mediterranean recipes.'],
            ['name' => 'Butter', 'slug' => 'butter', 'description' => 'Dairy product used for cooking, baking, sauces, and spreading.'],
            ['name' => 'All-Purpose Flour', 'slug' => 'all-purpose-flour', 'description' => 'Versatile wheat flour used for baking, bread, sauces, pancakes, and doughs.'],
            ['name' => 'Milk', 'slug' => 'milk', 'description' => 'Dairy milk commonly used in beverages, desserts, sauces, soups, and baking.'],
            ['name' => 'Lemon', 'slug' => 'lemon', 'description' => 'Citrus fruit used for juice, zest, marinades, dressings, sauces, and desserts.'],
            ['name' => 'Basil', 'slug' => 'basil', 'description' => 'Aromatic herb commonly used in Italian, Mediterranean, and other cuisines.'],
        ];

        foreach ($ingredients as $ingredient) {
            Ingredient::query()->firstOrCreate(
                ['slug' => $ingredient['slug']],
                ['name' => $ingredient['name'], 'description' => $ingredient['description']],
            );
        }
    }
}
