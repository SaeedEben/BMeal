<?php

namespace App\Policies\Plan;

use App\Models\Plan\MealPlanItem;
use App\Models\User\User;

class MealPlanItemPolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.plan.meal_plan_item.index', MealPlanItem::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.plan.meal_plan_item.store', MealPlanItem::class);
    }

    public function PanelShow(User $user, MealPlanItem $model): bool
    {
        return $user->can('panel.plan.meal_plan_item.show', $model);
    }

    public function PanelUpdate(User $user, MealPlanItem $model): bool
    {
        return $user->can('panel.plan.meal_plan_item.update', $model);
    }

    public function PanelDelete(User $user, MealPlanItem $model): bool
    {
        return $user->can('panel.plan.meal_plan_item.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.plan.meal_plan_item.list', MealPlanItem::class);
    }

    public function PanelChangeStatus(User $user, MealPlanItem $model): bool
    {
        return $user->can('panel.plan.meal_plan_item.change_status', $model);
    }
}
