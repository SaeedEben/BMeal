<?php

namespace App\Policies;

use App\Models\MealPlanItem;
use App\Models\User;

class MealPlanItemPolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.meal_plan_item.index', MealPlanItem::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.meal_plan_item.store', MealPlanItem::class);
    }

    public function PanelShow(User $user, MealPlanItem $model): bool
    {
        return $user->can('panel.meal_plan_item.show', $model);
    }

    public function PanelUpdate(User $user, MealPlanItem $model): bool
    {
        return $user->can('panel.meal_plan_item.update', $model);
    }

    public function PanelDelete(User $user, MealPlanItem $model): bool
    {
        return $user->can('panel.meal_plan_item.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.meal_plan_item.list', MealPlanItem::class);
    }

    public function PanelChangeStatus(User $user, MealPlanItem $model): bool
    {
        return $user->can('panel.meal_plan_item.change_status', $model);
    }
}
