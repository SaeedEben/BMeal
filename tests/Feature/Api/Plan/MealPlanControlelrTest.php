<?php

namespace Tests\Feature\Api\Plan;

use App\Enum\Common\StatusEnum;
use App\Models\Plan\MealPlan;
use App\Models\Recipe\Recipe;
use App\Models\User\Role;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MealPlanControlelrTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_only_meal_plans_for_the_current_week_when_week_is_null(): void
    {
        $user = $this->createCustomer();
        $currentWeekPlan = $this->createMealPlan($user, now()->startOfWeek()->toDateString());
        $this->createMealPlan($user, now()->addWeek()->startOfWeek()->toDateString());

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/plan/meal_plans');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $currentWeekPlan->id);
    }

    public function test_index_returns_only_meal_plans_for_the_selected_week(): void
    {
        $user = $this->createCustomer();
        $this->createMealPlan($user, now()->startOfWeek()->toDateString());
        $selectedWeekStart = now()->addWeeks(2)->startOfWeek();
        $selectedWeekPlan = $this->createMealPlan($user, $selectedWeekStart->toDateString());

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/plan/meal_plans?week='.$selectedWeekStart->copy()->addDays(3)->toDateString());

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $selectedWeekPlan->id);
    }

    public function test_store_reuses_one_meal_plan_per_user_and_week(): void
    {
        $user = $this->createCustomer();
        $anotherUser = $this->createCustomer();
        $breakfastRecipe = $this->createRecipe('Breakfast recipe');
        $dinnerRecipe = $this->createRecipe('Dinner recipe');
        $weekStartDate = now()->addWeeks(2)->startOfWeek()->toDateString();

        $firstResponse = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/plan/meal_plans', [
                'week_start_date' => $weekStartDate,
                'items' => [[
                    'recipe_id' => $breakfastRecipe->id,
                    'date' => $weekStartDate.' 08:00',
                    'meal_type' => 'breakfast',
                    'servings' => 1,
                ]],
            ]);

        $firstResponse
            ->assertOk()
            ->assertJsonCount(1, 'data.items');

        $mealPlanId = $firstResponse->json('data.id');
        $this->assertTrue(
            MealPlan::query()
                ->whereKey($mealPlanId)
                ->whereDate('week_start_date', $weekStartDate)
                ->exists(),
        );

        $this->postJson('/api/v1/plan/meal_plans', [
            'week_start_date' => $weekStartDate,
            'items' => [[
                'recipe_id' => $dinnerRecipe->id,
                'date' => $weekStartDate.' 18:00',
                'meal_type' => 'dinner',
                'servings' => 2,
            ]],
        ])
            ->assertOk()
            ->assertJsonPath('data.id', $mealPlanId)
            ->assertJsonCount(2, 'data.items');

        $this->actingAs($anotherUser, 'sanctum')
            ->postJson('/api/v1/plan/meal_plans', [
                'week_start_date' => $weekStartDate,
                'items' => [[
                    'recipe_id' => $dinnerRecipe->id,
                    'date' => $weekStartDate.' 19:00',
                    'meal_type' => 'dinner',
                    'servings' => 1,
                ]],
            ])
            ->assertOk();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/plan/meal_plans?week='.$weekStartDate)
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $mealPlanId);

        $this->assertDatabaseCount('meal_plans', 2);
        $this->assertDatabaseCount('meal_plan_items', 3);
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

    private function createMealPlan(User $user, string $weekStartDate): MealPlan
    {
        return MealPlan::query()->create([
            'user_id' => $user->id,
            'week_start_date' => $weekStartDate,
            'status' => StatusEnum::ACTIVE,
        ]);
    }
}
