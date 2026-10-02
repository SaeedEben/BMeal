<?php

namespace App\Policies;

use App\Models\Ingredient;
use App\Models\User;

class IngredientPolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.ingredient.index', Ingredient::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.ingredient.store', Ingredient::class);
    }

    public function PanelShow(User $user, Ingredient $model): bool
    {
        return $user->can('panel.ingredient.show', $model);
    }

    public function PanelUpdate(User $user, Ingredient $model): bool
    {
        return $user->can('panel.ingredient.update', $model);
    }

    public function PanelDelete(User $user, Ingredient $model): bool
    {
        return $user->can('panel.ingredient.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.ingredient.list', Ingredient::class);
    }

    public function PanelChangeStatus(User $user, Ingredient $model): bool
    {
        return $user->can('panel.ingredient.change_status', $model);
    }
}
