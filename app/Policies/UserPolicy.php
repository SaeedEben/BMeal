<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function PanelIndex(User $user) :bool
    {
        return $user->can('panel.user.index', User::class);
    }


    /**
     * Determine whether the user can create models.
     */
    public function PanelStore(User $user) :bool
    {
        return $user->can('panel.user.store', User::class);
    }


    /**
     * Determine whether the user can view the model.
     */
    public function PanelShow(User $user, User $model) :bool
    {
        return $user->can('panel.user.show', $model);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function PanelUpdate(User $user, User $model) :bool
    {
        return $user->can('panel.user.update', $model);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function PanelDelete(User $user, User $model) :bool
    {
        return $user->can('panel.user.destroy', $model);
    }

    public function PanelList(User $user) :bool
    {
        return $user->can('panel.user.list', User::class);
    }

     public function PanelChangeStatus(User $user, User $model) :bool
    {
        return $user->can('panel.user.change_status', $model);
    }

     public function PanelChangePassword(User $user, User $model) :bool
    {
        return $user->can('panel.user.change_password', $model);
    }
}
