<?php

namespace App\Policies;

use App\Models\RecipeMealType;
use App\Models\User;

class RecipeMealTypePolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.recipe_meal_type.index', RecipeMealType::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.recipe_meal_type.store', RecipeMealType::class);
    }

    public function PanelShow(User $user, RecipeMealType $model): bool
    {
        return $user->can('panel.recipe_meal_type.show', $model);
    }

    public function PanelUpdate(User $user, RecipeMealType $model): bool
    {
        return $user->can('panel.recipe_meal_type.update', $model);
    }

    public function PanelDelete(User $user, RecipeMealType $model): bool
    {
        return $user->can('panel.recipe_meal_type.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.recipe_meal_type.list', RecipeMealType::class);
    }

    public function PanelChangeStatus(User $user, RecipeMealType $model): bool
    {
        return $user->can('panel.recipe_meal_type.change_status', $model);
    }
}
