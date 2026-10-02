<?php

namespace App\Policies;

use App\Models\RecipeTag;
use App\Models\User;

class RecipeTagPolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.recipe_tag.index', RecipeTag::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.recipe_tag.store', RecipeTag::class);
    }

    public function PanelShow(User $user, RecipeTag $model): bool
    {
        return $user->can('panel.recipe_tag.show', $model);
    }

    public function PanelUpdate(User $user, RecipeTag $model): bool
    {
        return $user->can('panel.recipe_tag.update', $model);
    }

    public function PanelDelete(User $user, RecipeTag $model): bool
    {
        return $user->can('panel.recipe_tag.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.recipe_tag.list', RecipeTag::class);
    }

    public function PanelChangeStatus(User $user, RecipeTag $model): bool
    {
        return $user->can('panel.recipe_tag.change_status', $model);
    }
}
