<?php

namespace Tests\Feature\Database\Seeders;

use App\Enum\File\TypeEnum;
use App\Models\Recipe\Ingredient;
use Database\Seeders\IngredientSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IngredientSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_registers_and_links_ingredient_images_idempotently(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('ingredients/Chicken Breast.png', 'ingredient image');
        Storage::disk('public')->put('ingredients/Chicken Breast.webp', 'alternate image');
        Storage::disk('public')->put('ingredients/Beef.jpg', 'ingredient image');
        Storage::disk('public')->put('ingredients/Salmon.webp', 'ingredient image');

        $this->seed(IngredientSeeder::class);

        $expectedImagePaths = [
            'chicken-breast' => 'ingredients/Chicken Breast.png',
            'beef' => 'ingredients/Beef.jpg',
            'salmon' => 'ingredients/Salmon.webp',
        ];

        foreach ($expectedImagePaths as $slug => $imagePath) {
            $file = Ingredient::query()->where('slug', $slug)->firstOrFail()->image;

            $this->assertNotNull($file);
            $this->assertSame($imagePath, $file->storage_path);
            $this->assertSame(TypeEnum::INGREDIENT_PHOTO, $file->file_type);
        }

        $this->seed(IngredientSeeder::class);

        $this->assertDatabaseCount('files', count($expectedImagePaths));

        foreach ($expectedImagePaths as $slug => $imagePath) {
            $ingredient = Ingredient::query()->where('slug', $slug)->firstOrFail();

            $this->assertSame($imagePath, $ingredient->image->storage_path);
        }
    }
}
