<?php

namespace App\Policies\Recipe;

use App\Models\Recipe\Category;
use App\Models\User\User;

class CategoryPolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.category.index', Category::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.category.store', Category::class);
    }

    public function PanelShow(User $user, Category $model): bool
    {
        return $user->can('panel.category.show', $model);
    }

    public function PanelUpdate(User $user, Category $model): bool
    {
        return $user->can('panel.category.update', $model);
    }

    public function PanelDelete(User $user, Category $model): bool
    {
        return $user->can('panel.category.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.category.list', Category::class);
    }

    public function PanelChangeStatus(User $user, Category $model): bool
    {
        return $user->can('panel.category.change_status', $model);
    }
}
