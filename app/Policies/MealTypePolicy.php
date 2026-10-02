<?php

namespace App\Policies;

use App\Models\MealType;
use App\Models\User;

class MealTypePolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.meal_type.index', MealType::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.meal_type.store', MealType::class);
    }

    public function PanelShow(User $user, MealType $model): bool
    {
        return $user->can('panel.meal_type.show', $model);
    }

    public function PanelUpdate(User $user, MealType $model): bool
    {
        return $user->can('panel.meal_type.update', $model);
    }

    public function PanelDelete(User $user, MealType $model): bool
    {
        return $user->can('panel.meal_type.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.meal_type.list', MealType::class);
    }

    public function PanelChangeStatus(User $user, MealType $model): bool
    {
        return $user->can('panel.meal_type.change_status', $model);
    }
}
