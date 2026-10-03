<?php

namespace App\Policies\Recipe;

use App\Models\Recipe\Recipe;
use App\Models\User\User;

class RecipePolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.recipe.recipe.index', Recipe::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.recipe.recipe.store', Recipe::class);
    }

    public function PanelShow(User $user, Recipe $model): bool
    {
        return $user->can('panel.recipe.recipe.show', $model);
    }

    public function PanelUpdate(User $user, Recipe $model): bool
    {
        return $user->can('panel.recipe.recipe.update', $model);
    }

    public function PanelDelete(User $user, Recipe $model): bool
    {
        return $user->can('panel.recipe.recipe.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.recipe.recipe.list', Recipe::class);
    }

    public function PanelChangeStatus(User $user, Recipe $model): bool
    {
        return $user->can('panel.recipe.recipe.change_status', $model);
    }
}
