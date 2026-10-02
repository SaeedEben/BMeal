<?php

namespace App\Policies\Recipe;

use App\Models\Recipe\RecipeCategory;
use App\Models\User\User;

class RecipeCategoryPolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.recipe_category.index', RecipeCategory::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.recipe_category.store', RecipeCategory::class);
    }

    public function PanelShow(User $user, RecipeCategory $model): bool
    {
        return $user->can('panel.recipe_category.show', $model);
    }

    public function PanelUpdate(User $user, RecipeCategory $model): bool
    {
        return $user->can('panel.recipe_category.update', $model);
    }

    public function PanelDelete(User $user, RecipeCategory $model): bool
    {
        return $user->can('panel.recipe_category.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.recipe_category.list', RecipeCategory::class);
    }

    public function PanelChangeStatus(User $user, RecipeCategory $model): bool
    {
        return $user->can('panel.recipe_category.change_status', $model);
    }
}
