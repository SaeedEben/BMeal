<?php

namespace Tests\Feature\Api\Recipe\Ingredients;

use App\Enum\Recipe\UnitTypeEnum;
use App\Http\Resources\Api\Recipe\Ingredients\RecipeIngredientResource;
use App\Models\Recipe\Ingredient;
use App\Models\Recipe\Recipe;
use App\Models\Recipe\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RecipeIngredientResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_resource_returns_the_unit_related_to_the_recipe_ingredient_pivot(): void
    {
        $recipe = Recipe::query()->create([
            'name' => 'Vegetable Soup',
            'slug' => 'vegetable-soup',
        ]);
        $ingredient = Ingredient::query()->create([
            'name' => 'Water',
            'slug' => 'water',
        ]);
        $unit = Unit::query()->create([
            'name' => 'Milliliter',
            'symbol' => 'ml',
            'slug' => 'milliliter',
            'type' => UnitTypeEnum::VOLUME,
        ]);

        DB::table('recipe_ingredients')->insert([
            'recipe_id' => $recipe->id,
            'ingredient_id' => $ingredient->id,
            'unit_id' => $unit->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ingredientWithPivot = $recipe->ingredients()->firstOrFail();
        $unitResource = (new RecipeIngredientResource($ingredientWithPivot->pivot))
            ->toArray(request())['unit'];

        $this->assertSame($unit->id, $unitResource->resolve()['id']);
    }
}
