<?php

namespace App\Policies\Recipe;

use App\Models\Recipe\Ingredient;
use App\Models\User\User;

class IngredientPolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.recipe.ingredient.index', Ingredient::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.recipe.ingredient.store', Ingredient::class);
    }

    public function PanelShow(User $user, Ingredient $model): bool
    {
        return $user->can('panel.recipe.ingredient.show', $model);
    }

    public function PanelUpdate(User $user, Ingredient $model): bool
    {
        return $user->can('panel.recipe.ingredient.update', $model);
    }

    public function PanelDelete(User $user, Ingredient $model): bool
    {
        return $user->can('panel.recipe.ingredient.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.recipe.ingredient.list', Ingredient::class);
    }

    public function PanelChangeStatus(User $user, Ingredient $model): bool
    {
        return $user->can('panel.recipe.ingredient.change_status', $model);
    }
}
