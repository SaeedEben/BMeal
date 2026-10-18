<?php

namespace App\Console\Commands\Recipe;

use App\Models\Country\Country;
use App\Models\Recipe\Category;
use App\Models\Recipe\Ingredient;
use App\Models\Recipe\Recipe;
use App\Models\Recipe\Tag;
use App\Models\Recipe\Unit;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('app:read-recipes {address : The address of the recipe file}')]
#[Description('Insert Recipes to database')]
class RecipeReader extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $address = $this->argument('address');

        $json = file_get_contents(storage_path("app/private/recipes/{$address}"));

        $data = json_decode($json, true);

        $country = explode('/', $address)[0];

        $countryId = Country::query()->where('slug', $country)->firstOrFail()->id;

        foreach ($data as $recipe) {
            $this->info("Inserting recipe: {$recipe['name']}");
             $newRecipe =  DB::transaction(function() use($recipe, $countryId){
               $newRecipe = Recipe::query()->updateOrCreate(
                [
                    'slug' => $recipe['slug'],
                ],
                [
                    "name"                 => $recipe["name"],
                    "slug"                 => $recipe["slug"],
                    "country_id"           => $countryId,
                    "meal_type"            => $recipe["meal_type"],
                    "status"               => $recipe["status"],
                    "description"          => $recipe["description"],
                    "prep_time_minutes"    => $recipe["prep_time_minutes"],
                    "cook_time_minutes"    => $recipe["cook_time_minutes"],
                    "servings"             => $recipe["servings"],
                    "difficulty"           => $recipe["difficulty"],
                    "calories_per_serving" => $recipe["calories_per_serving"],
                ]);

                return $newRecipe;
            });

            $categories = Category::query()->whereIn('name', $recipe['categories'])->pluck('id')->toArray();
            $newRecipe->categories()->syncWithoutDetaching($categories);

            $tags = Tag::query()->whereIn('name', $recipe['tags'])->pluck('id')->toArray();
            $newRecipe->tags()->syncWithoutDetaching($tags);

            foreach ($recipe['steps'] as $step){;
                $this->info("add step: {$step['step_number']}");
                $newRecipe->steps()->updateOrCreate([
                    "step_number" => $step["step_number"],
                    "instruction" => $step["instruction"],
                    "image"       => $step["image"],
                    "status"      => $step["status"],
                ]);
            }

            foreach ($recipe['ingredients'] as $ingredient){
                $this->info("add ingredient: {$ingredient['sort_order']} - {$ingredient['ingredient']}");
                $unit = Unit::query()->where('slug' , $ingredient["unit"])->first();

                $ingredientModel = Ingredient::query()
                    ->where('name', $ingredient['ingredient'])
                    ->firstOrFail();

                $newRecipe->ingredients()->syncWithoutDetaching([
                    $ingredientModel->id => [
                        'unit_id'    => $unit->id,
                        'quantity'   => $ingredient['quantity'],
                        'notes'      => $ingredient['notes'],
                        'sort_order' => $ingredient['sort_order'],
                        'status'     => $ingredient['status'],
                    ],
                ]);
            }
        }
    }
}
