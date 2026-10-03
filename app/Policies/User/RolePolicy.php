<?php

namespace App\Policies\User;

use App\Models\User\Role;
use App\Models\User\User;

class RolePolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.user.role.index', Role::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.user.role.store', Role::class);
    }

    public function PanelShow(User $user, Role $model): bool
    {
        return $user->can('panel.user.role.show', $model);
    }

    public function PanelUpdate(User $user, Role $model): bool
    {
        return $user->can('panel.user.role.update', $model);
    }

    public function PanelDelete(User $user, Role $model): bool
    {
        return $user->can('panel.user.role.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.user.role.list', Role::class);
    }

    public function PanelChangeStatus(User $user, Role $model): bool
    {
        return $user->can('panel.user.role.change_status', $model);
    }
}
