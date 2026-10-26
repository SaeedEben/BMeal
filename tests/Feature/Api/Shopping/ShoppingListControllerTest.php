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
            ->assertJsonPath('data.week_date', $weekStart)
            ->assertJsonCount(0, 'data.items');

        $this->assertDatabaseHas('shopping_lists', [
            'user_id' => $user->id,
            'week_date' => $weekStart,
            'meal_plan_id' => null,
        ]);
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
        $secondMealPlan = $this->createMealPlan($user, $weekStart->toDateString());
        $this->addMeal($secondMealPlan, $thirdRecipe, $weekStart->copy()->addDays(3)->toDateString());
        $otherUsersMealPlan = $this->createMealPlan($otherUser, $weekStart->toDateString());
        $this->addMeal($otherUsersMealPlan, $otherUsersRecipe, $weekStart->toDateString());

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/shopping/shopping_list')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('week_start_date');

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/shopping/shopping_list', ['week_start_date' => $weekStart->toDateString()]);

        $response
            ->assertOk()
            ->assertJsonPath('data.week_date', $weekStart->toDateString())
            ->assertJsonCount(3, 'data.items')
            ->assertJsonFragment(['name' => 'Avocado', 'quantity' => '2.00', 'unit' => 'pc'])
            ->assertJsonFragment(['name' => 'Onion', 'quantity' => '3.00', 'unit' => 'pc']);

        $shoppingList = ShoppingList::query()->sole();
        $this->getJson('/api/v1/shopping/shopping_list?week='.$weekStart->copy()->addDays(3)->toDateString())
            ->assertOk()
            ->assertJsonPath('data.id', $shoppingList->id)
            ->assertJsonCount(3, 'data.items');

        $avocadoItem = $shoppingList->items()->where('ingredient_id', $avocado->id)->sole();
        $avocadoItem->update(['is_completed' => true]);
        $shoppingList->items()->create([
            'name' => 'Olive oil',
            'quantity' => '1.00',
            'unit' => 'bottle',
            'is_completed' => false,
            'status' => StatusEnum::ACTIVE,
        ]);
        $secondMeal->delete();
        $this->addMeal($mealPlan, $thirdRecipe, $weekStart->copy()->addDays(3)->toDateString());

        $updatedResponse = $this->postJson('/api/v1/shopping/shopping_list', ['week_start_date' => $weekStart->toDateString()]);

        $updatedResponse
            ->assertOk()
            ->assertJsonPath('data.id', $shoppingList->id)
            ->assertJsonCount(3, 'data.items')
            ->assertJsonFragment(['name' => 'Avocado', 'quantity' => '1.00', 'unit' => 'pc'])
            ->assertJsonFragment(['name' => 'Olive oil', 'quantity' => '1.00', 'unit' => 'bottle'])
            ->assertJsonFragment(['name' => 'Onion', 'quantity' => '6.00', 'unit' => 'pc']);

        $this->assertDatabaseCount('shopping_lists', 1);
        $this->assertDatabaseHas('shopping_lists', [
            'id' => $shoppingList->id,
            'meal_plan_id' => $mealPlan->id,
        ]);
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
            'meal_plan_id' => $otherUsersMealPlan->id,
            'week_date' => $weekStart->toDateString(),
            'name' => 'Other user shopping list',
            'status' => StatusEnum::ACTIVE,
        ]);

        $this->getJson('/api/v1/shopping/shopping_list?week='.$weekStart->copy()->addDays(3)->toDateString())
            ->assertOk()
            ->assertJsonPath('data.id', $shoppingList->id)
            ->assertJsonCount(3, 'data.items');
    }

    public function test_check_updates_owned_items_and_rejects_foreign_items_atomically(): void
    {
        $user = $this->createCustomer();
        $otherUser = $this->createCustomer();
        $weekStart = now()->startOfWeek()->toDateString();
        $mealPlan = $this->createMealPlan($user, $weekStart);
        $otherUsersMealPlan = $this->createMealPlan($otherUser, $weekStart);
        $shoppingList = ShoppingList::query()->create([
            'user_id' => $user->id,
            'meal_plan_id' => $mealPlan->id,
            'week_date' => $weekStart,
            'name' => 'Shopping List',
            'status' => StatusEnum::ACTIVE,
        ]);
        $otherUsersShoppingList = ShoppingList::query()->create([
            'user_id' => $otherUser->id,
            'meal_plan_id' => $otherUsersMealPlan->id,
            'week_date' => $weekStart,
            'name' => 'Other Shopping List',
            'status' => StatusEnum::ACTIVE,
        ]);
        $firstItem = $shoppingList->items()->create([
            'name' => 'Avocado',
            'is_completed' => false,
            'status' => StatusEnum::ACTIVE,
        ]);
        $secondItem = $shoppingList->items()->create([
            'name' => 'Tomato',
            'is_completed' => true,
            'status' => StatusEnum::ACTIVE,
        ]);
        $foreignItem = $otherUsersShoppingList->items()->create([
            'name' => 'Onion',
            'is_completed' => false,
            'status' => StatusEnum::ACTIVE,
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/shopping/shopping_list/check_item', [
                'items' => [
                    ['item_id' => $firstItem->id, 'is_completed' => 'done'],
                ],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('items.0.is_completed');

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/shopping/shopping_list/check_item', [
                'items' => [
                    ['item_id' => $firstItem->id, 'is_completed' => true],
                    ['item_id' => $secondItem->id, 'is_completed' => false],
                ],
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('shopping_list_items', [
            'id' => $firstItem->id,
            'is_completed' => true,
        ]);
        $this->assertDatabaseHas('shopping_list_items', [
            'id' => $secondItem->id,
            'is_completed' => false,
        ]);

        $this->postJson('/api/v1/shopping/shopping_list/check_item', [
            'items' => [
                ['item_id' => $firstItem->id, 'is_completed' => false],
                ['item_id' => $foreignItem->id, 'is_completed' => true],
            ],
        ])->assertNotFound();

        $this->assertDatabaseHas('shopping_list_items', [
            'id' => $firstItem->id,
            'is_completed' => true,
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

    private function addMeal(MealPlan $mealPlan, Recipe $recipe, string $date): MealPlanItem
    {
        return MealPlanItem::query()->create([
            'meal_plan_id' => $mealPlan->id,
            'recipe_id' => $recipe->id,
            'date' => $date,
            'meal_type' => 'dinner',
            'servings' => 1,
            'status' => StatusEnum::ACTIVE,
        ]);
    }
}
