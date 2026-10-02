<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.role.index', Role::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.role.store', Role::class);
    }

    public function PanelShow(User $user, Role $model): bool
    {
        return $user->can('panel.role.show', $model);
    }

    public function PanelUpdate(User $user, Role $model): bool
    {
        return $user->can('panel.role.update', $model);
    }

    public function PanelDelete(User $user, Role $model): bool
    {
        return $user->can('panel.role.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.role.list', Role::class);
    }

    public function PanelChangeStatus(User $user, Role $model): bool
    {
        return $user->can('panel.role.change_status', $model);
    }
}
