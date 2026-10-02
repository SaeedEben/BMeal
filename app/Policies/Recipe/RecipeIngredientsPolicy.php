<?php

namespace App\Policies\Recipe;

use App\Models\Recipe\RecipeIngredients;
use App\Models\User\User;

class RecipeIngredientsPolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.recipe_ingredients.index', RecipeIngredients::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.recipe_ingredients.store', RecipeIngredients::class);
    }

    public function PanelShow(User $user, RecipeIngredients $model): bool
    {
        return $user->can('panel.recipe_ingredients.show', $model);
    }

    public function PanelUpdate(User $user, RecipeIngredients $model): bool
    {
        return $user->can('panel.recipe_ingredients.update', $model);
    }

    public function PanelDelete(User $user, RecipeIngredients $model): bool
    {
        return $user->can('panel.recipe_ingredients.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.recipe_ingredients.list', RecipeIngredients::class);
    }

    public function PanelChangeStatus(User $user, RecipeIngredients $model): bool
    {
        return $user->can('panel.recipe_ingredients.change_status', $model);
    }
}
