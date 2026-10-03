<?php

namespace App\Policies\User;

use App\Models\User\User;
use App\Models\User\UserFavorite;

class UserFavoritePolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.user.user_favorite.index', UserFavorite::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.user.user_favorite.store', UserFavorite::class);
    }

    public function PanelShow(User $user, UserFavorite $model): bool
    {
        return $user->can('panel.user.user_favorite.show', $model);
    }

    public function PanelUpdate(User $user, UserFavorite $model): bool
    {
        return $user->can('panel.user.user_favorite.update', $model);
    }

    public function PanelDelete(User $user, UserFavorite $model): bool
    {
        return $user->can('panel.user.user_favorite.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.user.user_favorite.list', UserFavorite::class);
    }

    public function PanelChangeStatus(User $user, UserFavorite $model): bool
    {
        return $user->can('panel.user.user_favorite.change_status', $model);
    }
}
