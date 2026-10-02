<?php

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;

class UnitPolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.unit.index', Unit::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.unit.store', Unit::class);
    }

    public function PanelShow(User $user, Unit $model): bool
    {
        return $user->can('panel.unit.show', $model);
    }

    public function PanelUpdate(User $user, Unit $model): bool
    {
        return $user->can('panel.unit.update', $model);
    }

    public function PanelDelete(User $user, Unit $model): bool
    {
        return $user->can('panel.unit.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.unit.list', Unit::class);
    }

    public function PanelChangeStatus(User $user, Unit $model): bool
    {
        return $user->can('panel.unit.change_status', $model);
    }
}
