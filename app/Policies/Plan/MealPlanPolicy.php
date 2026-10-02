<?php

namespace App\Policies\Plan;

use App\Models\Plan\MealPlan;
use App\Models\User\User;

class MealPlanPolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.meal_plan.index', MealPlan::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.meal_plan.store', MealPlan::class);
    }

    public function PanelShow(User $user, MealPlan $model): bool
    {
        return $user->can('panel.meal_plan.show', $model);
    }

    public function PanelUpdate(User $user, MealPlan $model): bool
    {
        return $user->can('panel.meal_plan.update', $model);
    }

    public function PanelDelete(User $user, MealPlan $model): bool
    {
        return $user->can('panel.meal_plan.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.meal_plan.list', MealPlan::class);
    }

    public function PanelChangeStatus(User $user, MealPlan $model): bool
    {
        return $user->can('panel.meal_plan.change_status', $model);
    }
}
