<?php

namespace App\Policies\User;

use App\Models\User\Permission;
use App\Models\User\User;

class PermissionPolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.user.permission.index', Permission::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.user.permission.store', Permission::class);
    }

    public function PanelShow(User $user, Permission $model): bool
    {
        return $user->can('panel.user.permission.show', $model);
    }

    public function PanelUpdate(User $user, Permission $model): bool
    {
        return $user->can('panel.user.permission.update', $model);
    }

    public function PanelDelete(User $user, Permission $model): bool
    {
        return $user->can('panel.user.permission.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.user.permission.list', Permission::class);
    }

    public function PanelChangeStatus(User $user, Permission $model): bool
    {
        return $user->can('panel.user.permission.change_status', $model);
    }
}
