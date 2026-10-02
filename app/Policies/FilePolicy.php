<?php

namespace App\Policies;

use App\Models\File;
use App\Models\User;

class FilePolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.file.index', File::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.file.store', File::class);
    }

    public function PanelShow(User $user, File $model): bool
    {
        return $user->can('panel.file.show', $model);
    }

    public function PanelUpdate(User $user, File $model): bool
    {
        return $user->can('panel.file.update', $model);
    }

    public function PanelDelete(User $user, File $model): bool
    {
        return $user->can('panel.file.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.file.list', File::class);
    }

    public function PanelChangeStatus(User $user, File $model): bool
    {
        return $user->can('panel.file.change_status', $model);
    }
}
