<?php

namespace App\Policies;

use App\Models\RecipeSteps;
use App\Models\User;

class RecipeStepsPolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.recipe_steps.index', RecipeSteps::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.recipe_steps.store', RecipeSteps::class);
    }

    public function PanelShow(User $user, RecipeSteps $model): bool
    {
        return $user->can('panel.recipe_steps.show', $model);
    }

    public function PanelUpdate(User $user, RecipeSteps $model): bool
    {
        return $user->can('panel.recipe_steps.update', $model);
    }

    public function PanelDelete(User $user, RecipeSteps $model): bool
    {
        return $user->can('panel.recipe_steps.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.recipe_steps.list', RecipeSteps::class);
    }

    public function PanelChangeStatus(User $user, RecipeSteps $model): bool
    {
        return $user->can('panel.recipe_steps.change_status', $model);
    }
}
