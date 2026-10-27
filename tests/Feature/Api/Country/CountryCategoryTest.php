<?php

namespace Tests\Feature\Api\Country;

use App\Enum\File\StorageEnum;
use App\Enum\File\TypeEnum;
use App\Models\Country\Country;
use App\Models\File\File;
use App\Models\Recipe\Recipe;
use App\Models\User\Role;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CountryCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_seven_countries_with_one_recipe_image_each(): void
    {
        $this->getJson('/api/v1/country/category')->assertUnauthorized();

        $user = User::query()->create([
            'full_name' => 'Test Customer',
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
        ]);
        $user->assignRole(Role::query()->firstOrCreate(['name' => 'customer', 'guard_name' => 'web']));

        $expectedImages = [];
        $expectedCountries = [];

        foreach (range(1, 7) as $countryNumber) {
            $country = Country::query()->create([
                'name' => 'Country '.$countryNumber,
                'code' => 'C'.(string) $countryNumber,
                'slug' => 'country-'.$countryNumber,
                'description' => 'Country '.$countryNumber.' meals',
            ]);
            $expectedCountries[$country->id] = [
                'name' => $country->name,
                'description' => $country->description,
            ];

            foreach (range(1, 2) as $recipeNumber) {
                $image = File::query()->create([
                    'name' => 'Recipe image',
                    'file_type' => TypeEnum::MEAL_PHOTO->value,
                    'storage_path' => "recipes/country-{$countryNumber}-{$recipeNumber}.jpg",
                    'storage_provider' => StorageEnum::LOCAL->value,
                ]);
                Recipe::query()->create([
                    'country_id' => $country->id,
                    'name' => "Recipe {$recipeNumber}",
                    'slug' => "country-{$countryNumber}-recipe-{$recipeNumber}",
                    'image_id' => $image->id,
                ]);

                $expectedImages[$country->id][] = $image->preview();
            }
        }

        Country::query()->create([
            'name' => 'Country without recipes',
            'code' => 'C8',
            'slug' => 'country-without-recipes',
        ]);
        $countryWithoutRecipeImage = Country::query()->create([
            'name' => 'Country without recipe image',
            'code' => 'C9',
            'slug' => 'country-without-recipe-image',
        ]);
        Recipe::query()->create([
            'country_id' => $countryWithoutRecipeImage->id,
            'name' => 'Recipe without image',
            'slug' => 'recipe-without-image',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/country/category');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(7, 'data');

        $countryIds = [];

        foreach ($response->json('data') as $countryCategory) {
            $countryId = $countryCategory['country_id'];

            $this->assertArrayHasKey($countryId, $expectedImages);
            $this->assertSame($expectedCountries[$countryId]['name'], $countryCategory['name']);
            $this->assertSame($expectedCountries[$countryId]['description'], $countryCategory['description']);
            $this->assertContains($countryCategory['image'], $expectedImages[$countryId]);
            $countryIds[] = $countryId;
        }

        $this->assertCount(7, array_unique($countryIds));
    }
}
