<?php

namespace App\Policies\User;

use App\Models\User\Permission;
use App\Models\User\User;

class PermissionPolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.permission.index', Permission::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.permission.store', Permission::class);
    }

    public function PanelShow(User $user, Permission $model): bool
    {
        return $user->can('panel.permission.show', $model);
    }

    public function PanelUpdate(User $user, Permission $model): bool
    {
        return $user->can('panel.permission.update', $model);
    }

    public function PanelDelete(User $user, Permission $model): bool
    {
        return $user->can('panel.permission.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.permission.list', Permission::class);
    }

    public function PanelChangeStatus(User $user, Permission $model): bool
    {
        return $user->can('panel.permission.change_status', $model);
    }
}
