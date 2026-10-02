<?php

namespace App\Policies;

use App\Models\Recipe;
use App\Models\User;

class RecipePolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.recipe.index', Recipe::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.recipe.store', Recipe::class);
    }

    public function PanelShow(User $user, Recipe $model): bool
    {
        return $user->can('panel.recipe.show', $model);
    }

    public function PanelUpdate(User $user, Recipe $model): bool
    {
        return $user->can('panel.recipe.update', $model);
    }

    public function PanelDelete(User $user, Recipe $model): bool
    {
        return $user->can('panel.recipe.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.recipe.list', Recipe::class);
    }

    public function PanelChangeStatus(User $user, Recipe $model): bool
    {
        return $user->can('panel.recipe.change_status', $model);
    }
}
