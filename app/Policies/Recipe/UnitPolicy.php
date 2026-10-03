<?php

namespace App\Policies\Recipe;

use App\Models\Recipe\Unit;
use App\Models\User\User;

class UnitPolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.recipe.unit.index', Unit::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.recipe.unit.store', Unit::class);
    }

    public function PanelShow(User $user, Unit $model): bool
    {
        return $user->can('panel.recipe.unit.show', $model);
    }

    public function PanelUpdate(User $user, Unit $model): bool
    {
        return $user->can('panel.recipe.unit.update', $model);
    }

    public function PanelDelete(User $user, Unit $model): bool
    {
        return $user->can('panel.recipe.unit.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.recipe.unit.list', Unit::class);
    }

    public function PanelChangeStatus(User $user, Unit $model): bool
    {
        return $user->can('panel.recipe.unit.change_status', $model);
    }
}
