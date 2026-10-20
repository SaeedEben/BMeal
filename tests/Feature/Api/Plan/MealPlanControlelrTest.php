<?php

namespace Tests\Feature\Api\Plan;

use App\Enum\Common\StatusEnum;
use App\Models\Plan\MealPlan;
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

    private function createCustomer(): User
    {
        $user = User::query()->create([
            'full_name' => 'Test Customer',
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
        ]);
        $user->assignRole(Role::query()->create(['name' => 'customer', 'guard_name' => 'web']));

        return $user;
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
