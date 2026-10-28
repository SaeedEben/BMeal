<?php

namespace Tests\Feature\Api\Shopping;

use App\Enum\Common\StatusEnum;
use App\Models\Plan\MealPlan;
use App\Models\Plan\MealPlanItem;
use App\Models\Recipe\Ingredient;
use App\Models\Recipe\Recipe;
use App\Models\Recipe\RecipeIngredients;
use App\Models\Recipe\Unit;
use App\Models\Shopping\ShoppingList;
use App\Models\User\Role;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShoppingListControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_returns_an_empty_array_when_the_week_has_no_shopping_list(): void
    {
        $user = $this->createCustomer();
        $week = now()->startOfWeek()->toDateString();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/shopping/shopping_list?week='.$week)
            ->assertOk()
            ->assertJsonPath('data', []);

        $this->assertDatabaseCount('shopping_lists', 0);
    }

    public function test_generate_returns_an_empty_list_when_the_user_has_no_meal_plans_for_the_week(): void
    {
        $user = $this->createCustomer();
        $weekStart = now()->addWeek()->startOfWeek()->toDateString();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/shopping/shopping_list', ['week_start_date' => $weekStart])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data', null);

        $this->assertDatabaseHas('shopping_lists', [
            'user_id' => $user->id,
            'week_date' => $weekStart,
        ]);
    }

    public function test_show_combines_same_name_items_across_dates_and_generate_returns_success_only(): void
    {
        $user = $this->createCustomer();
        $weekStart = now()->setDate(2026, 11, 2)->startOfWeek();
        $firstSalt = Ingredient::query()->create([
            'name' => 'Salt',
            'slug' => 'salt-first',
        ]);
        $secondSalt = Ingredient::query()->create([
            'name' => 'Salt',
            'slug' => 'salt-second',
        ]);
        $piece = Unit::query()->create([
            'name' => 'Piece',
            'symbol' => 'pc',
            'slug' => 'piece',
            'type' => 'count',
        ]);
        $firstRecipe = $this->createRecipe('First salt recipe');
        $secondRecipe = $this->createRecipe('Second salt recipe');

        $this->addIngredient($firstRecipe, $firstSalt, $piece, '2.00');
        $this->addIngredient($secondRecipe, $secondSalt, $piece, '3.00');

        $mealPlan = $this->createMealPlan($user, $weekStart->toDateString());
        $this->addMeal($mealPlan, $firstRecipe, $weekStart->toDateString());
        $this->addMeal($mealPlan, $secondRecipe, $weekStart->copy()->addDay()->toDateString());

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/shopping/shopping_list', ['week_start_date' => $weekStart->toDateString()])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data', null);

        $this->getJson('/api/v1/shopping/shopping_list?week='.$weekStart->toDateString())
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.name', 'Salt')
            ->assertJsonPath('data.items.0.quantity', '5.00')
            ->assertJsonPath('data.items.0.unit', 'pc');

        $this->postJson('/api/v1/shopping/shopping_list/check_item', [
            'date' => 'all_day',
            'week' => $weekStart->toDateString(),
            'items' => [[
                'item_name' => 'Salt',
                'is_completed' => true,
            ]],
        ])->assertOk();

        $this->assertSame(2, ShoppingList::query()->sole()->items()->where('is_completed', true)->count());
    }

    public function test_weekly_list_aggregates_and_reconciles_the_authenticated_users_meal_plan(): void
    {
        $user = $this->createCustomer();
        $otherUser = $this->createCustomer();
        $weekStart = now()->setDate(2026, 11, 2)->startOfWeek();
        $avocado = Ingredient::query()->create([
            'name' => 'Avocado',
            'slug' => 'avocado',
        ]);
        $tomato = Ingredient::query()->create([
            'name' => 'Tomato',
            'slug' => 'tomato',
        ]);
        $onion = Ingredient::query()->create([
            'name' => 'Onion',
            'slug' => 'onion',
        ]);
        $piece = Unit::query()->create([
            'name' => 'Piece',
            'symbol' => 'pc',
            'slug' => 'piece',
            'type' => 'count',
        ]);
        $firstRecipe = $this->createRecipe('First recipe');
        $secondRecipe = $this->createRecipe('Second recipe');
        $thirdRecipe = $this->createRecipe('Third recipe');
        $otherUsersRecipe = $this->createRecipe('Other user recipe');

        $this->addIngredient($firstRecipe, $avocado, $piece, '1.00');
        $this->addIngredient($secondRecipe, $avocado, $piece, '1.00');
        $this->addIngredient($secondRecipe, $tomato, $piece, '2.00');
        $this->addIngredient($thirdRecipe, $onion, $piece, '3.00');
        $this->addIngredient($otherUsersRecipe, $onion, $piece, '9.00');

        $mealPlan = $this->createMealPlan($user, $weekStart->toDateString());
        $secondMeal = $this->addMeal($mealPlan, $secondRecipe, $weekStart->copy()->addDays(2)->toDateString());
        $this->addMeal($mealPlan, $firstRecipe, $weekStart->copy()->addDays(1)->toDateString());
        $otherUsersMealPlan = $this->createMealPlan($otherUser, $weekStart->toDateString());
        $this->addMeal($otherUsersMealPlan, $otherUsersRecipe, $weekStart->toDateString());

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/shopping/shopping_list')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('week_start_date');

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/shopping/shopping_list', ['week_start_date' => $weekStart->toDateString()]);

        $this->assertDatabaseCount('shopping_lists', 1);

        $response = $this->getJson('/api/v1/shopping/shopping_list?week='.$weekStart->toDateString());

        $response
            ->assertOk()
            ->assertJsonPath('data.week_date', $weekStart->toDateString())
            ->assertJsonCount(2, 'data.items')
            ->assertJsonFragment([
                'name' => 'Avocado',
                'quantity' => '2.00',
                'unit' => 'pc',
                'date' => $weekStart->copy()->addDay()->toDateString(),
            ]);

        $shoppingList = ShoppingList::query()->sole();
        $this->getJson('/api/v1/shopping/shopping_list?week='.$weekStart->copy()->addDays(3)->toDateString())
            ->assertOk()
            ->assertJsonPath('data.id', $shoppingList->id)
            ->assertJsonCount(2, 'data.items');

        $avocadoItem = $shoppingList->items()
            ->where('ingredient_id', $avocado->id)
            ->whereDate('date', $weekStart->copy()->addDay()->toDateString())
            ->sole();
        $avocadoItem->update(['is_completed' => true]);
        $shoppingList->items()->create([
            'name' => 'Olive oil',
            'date' => $weekStart->copy()->addDays(4)->toDateString(),
            'quantity' => '1.00',
            'unit' => 'bottle',
            'is_completed' => false,
            'status' => StatusEnum::ACTIVE,
        ]);
        $secondMeal->delete();
        $this->addMeal($mealPlan, $thirdRecipe, $weekStart->copy()->addDays(3)->toDateString());

        $this->postJson('/api/v1/shopping/shopping_list', ['week_start_date' => $weekStart->toDateString()])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data', null);

        $updatedResponse = $this->getJson('/api/v1/shopping/shopping_list?week='.$weekStart->toDateString());

        $updatedResponse
            ->assertOk()
            ->assertJsonPath('data.id', $shoppingList->id)
            ->assertJsonCount(3, 'data.items')
            ->assertJsonFragment([
                'name' => 'Avocado',
                'quantity' => '1.00',
                'unit' => 'pc',
                'date' => $weekStart->copy()->addDay()->toDateString(),
            ])
            ->assertJsonFragment(['name' => 'Olive oil', 'quantity' => '1.00', 'unit' => 'bottle'])
            ->assertJsonFragment([
                'name' => 'Onion',
                'quantity' => '3.00',
                'unit' => 'pc',
                'date' => $weekStart->copy()->addDays(3)->toDateString(),
            ]);

        $this->assertDatabaseCount('shopping_lists', 1);
        $this->assertDatabaseHas('shopping_lists', ['id' => $shoppingList->id]);
        $this->assertDatabaseMissing('shopping_list_items', [
            'shopping_list_id' => $shoppingList->id,
            'ingredient_id' => $tomato->id,
        ]);
        $this->assertDatabaseMissing('shopping_list_items', [
            'shopping_list_id' => $shoppingList->id,
            'ingredient_id' => $onion->id,
            'quantity' => '9.00',
        ]);
        $this->assertDatabaseHas('shopping_list_items', [
            'id' => $avocadoItem->id,
            'is_completed' => true,
            'quantity' => '1.00',
        ]);

        ShoppingList::query()->create([
            'user_id' => $otherUser->id,
            'week_date' => $weekStart->toDateString(),
            'name' => 'Other user shopping list',
            'status' => StatusEnum::ACTIVE,
        ]);

        $this->getJson('/api/v1/shopping/shopping_list?week='.$weekStart->copy()->addDays(3)->toDateString())
            ->assertOk()
            ->assertJsonPath('data.id', $shoppingList->id)
            ->assertJsonCount(3, 'data.items');
    }

    public function test_generate_scales_ingredient_quantities_to_the_meal_plan_servings(): void
    {
        $user = $this->createCustomer();
        $weekStart = now()->startOfWeek();
        $ingredient = Ingredient::query()->create([
            'name' => 'Flour',
            'slug' => 'flour',
        ]);
        $cup = Unit::query()->create([
            'name' => 'Cup',
            'symbol' => 'cup',
            'slug' => 'cup',
            'type' => 'volume',
        ]);
        $recipe = $this->createRecipe('Bread');
        $recipe->update(['servings' => 4]);
        $this->addIngredient($recipe, $ingredient, $cup, '2.00');
        $mealPlan = $this->createMealPlan($user, $weekStart->toDateString());
        $this->addMeal($mealPlan, $recipe, $weekStart->toDateString(), 2);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/shopping/shopping_list', ['week_start_date' => $weekStart->toDateString()])
            ->assertOk();

        $this->assertDatabaseHas('shopping_list_items', [
            'name' => 'Flour',
            'quantity' => '1.00',
            'unit' => 'cup',
        ]);
    }

    public function test_check_updates_owned_items_and_rejects_foreign_items_atomically(): void
    {
        $user = $this->createCustomer();
        $otherUser = $this->createCustomer();
        $weekStart = now()->startOfWeek();
        $shoppingList = ShoppingList::query()->create([
            'user_id' => $user->id,
            'week_date' => $weekStart->toDateString(),
            'name' => 'Shopping List',
            'status' => StatusEnum::ACTIVE,
        ]);
        $otherUsersShoppingList = ShoppingList::query()->create([
            'user_id' => $otherUser->id,
            'week_date' => $weekStart->toDateString(),
            'name' => 'Other Shopping List',
            'status' => StatusEnum::ACTIVE,
        ]);
        $firstItem = $shoppingList->items()->create([
            'name' => 'Avocado',
            'date' => $weekStart->toDateString(),
            'is_completed' => false,
            'status' => StatusEnum::ACTIVE,
        ]);
        $secondAvocadoItem = $shoppingList->items()->create([
            'name' => 'Avocado',
            'date' => $weekStart->copy()->addDays(2)->toDateString(),
            'is_completed' => false,
            'status' => StatusEnum::ACTIVE,
        ]);
        $secondItem = $shoppingList->items()->create([
            'name' => 'Tomato',
            'date' => $weekStart->toDateString(),
            'is_completed' => true,
            'status' => StatusEnum::ACTIVE,
        ]);
        $foreignItem = $otherUsersShoppingList->items()->create([
            'name' => 'Onion',
            'date' => $weekStart->toDateString(),
            'is_completed' => false,
            'status' => StatusEnum::ACTIVE,
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/shopping/shopping_list/check_item', [
                'date' => 'monday',
                'week' => $weekStart->toDateString(),
                'items' => [
                    ['item_name' => 'Avocado', 'is_completed' => 'done'],
                ],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('items.0.is_completed');

        $this->postJson('/api/v1/shopping/shopping_list/check_item', [
            'date' => 'funday',
            'week' => $weekStart->toDateString(),
            'items' => [
                ['item_name' => 'Avocado', 'is_completed' => true],
            ],
        ])->assertUnprocessable()->assertJsonValidationErrors('date');

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/shopping/shopping_list/check_item', [
                'date' => 'monday',
                'week' => $weekStart->toDateString(),
                'items' => [
                    ['item_name' => 'Avocado', 'is_completed' => true],
                    ['item_name' => 'Tomato', 'is_completed' => false],
                ],
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('shopping_list_items', [
            'id' => $firstItem->id,
            'is_completed' => true,
        ]);
        $this->assertDatabaseHas('shopping_list_items', [
            'id' => $secondAvocadoItem->id,
            'is_completed' => false,
        ]);
        $this->assertDatabaseHas('shopping_list_items', [
            'id' => $secondItem->id,
            'is_completed' => false,
        ]);

        $this->postJson('/api/v1/shopping/shopping_list/check_item', [
            'date' => 'All day',
            'week' => $weekStart->toDateString(),
            'items' => [
                ['item_name' => 'Avocado', 'is_completed' => false],
            ],
        ])->assertOk();

        $this->assertDatabaseHas('shopping_list_items', [
            'id' => $firstItem->id,
            'is_completed' => false,
        ]);
        $this->assertDatabaseHas('shopping_list_items', [
            'id' => $secondAvocadoItem->id,
            'is_completed' => false,
        ]);

        $this->postJson('/api/v1/shopping/shopping_list/check_item', [
            'date' => 'all_day',
            'week' => $weekStart->toDateString(),
            'items' => [
                ['item_name' => 'Avocado', 'is_completed' => true],
                ['item_name' => 'Onion', 'is_completed' => true],
            ],
        ])->assertNotFound();

        $this->assertDatabaseHas('shopping_list_items', [
            'id' => $firstItem->id,
            'is_completed' => false,
        ]);
        $this->assertDatabaseHas('shopping_list_items', [
            'id' => $foreignItem->id,
            'is_completed' => false,
        ]);
    }

    private function createCustomer(): User
    {
        $user = User::query()->create([
            'full_name' => 'Test Customer',
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
        ]);
        $user->assignRole(Role::query()->firstOrCreate(['name' => 'customer', 'guard_name' => 'web']));

        return $user;
    }

    private function createRecipe(string $name): Recipe
    {
        return Recipe::query()->create([
            'name' => $name,
            'slug' => str($name)->slug()->toString(),
        ]);
    }

    private function addIngredient(Recipe $recipe, Ingredient $ingredient, Unit $unit, string $quantity): void
    {
        RecipeIngredients::query()->create([
            'recipe_id' => $recipe->id,
            'ingredient_id' => $ingredient->id,
            'unit_id' => $unit->id,
            'quantity' => $quantity,
            'status' => StatusEnum::ACTIVE,
        ]);
    }

    private function createMealPlan(User $user, string $weekStart): MealPlan
    {
        return MealPlan::query()->create([
            'user_id' => $user->id,
            'week_start_date' => $weekStart,
            'status' => StatusEnum::ACTIVE,
        ]);
    }

    private function addMeal(MealPlan $mealPlan, Recipe $recipe, string $date, int $servings = 1): MealPlanItem
    {
        return MealPlanItem::query()->create([
            'meal_plan_id' => $mealPlan->id,
            'recipe_id' => $recipe->id,
            'date' => $date,
            'meal_type' => 'dinner',
            'servings' => $servings,
            'status' => StatusEnum::ACTIVE,
        ]);
    }
}
