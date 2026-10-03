<?php

namespace App\Policies\File;

use App\Models\File\File;
use App\Models\User\User;

class FilePolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.file.file.index', File::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.file.file.store', File::class);
    }

    public function PanelShow(User $user, File $model): bool
    {
        return $user->can('panel.file.file.show', $model);
    }

    public function PanelUpdate(User $user, File $model): bool
    {
        return $user->can('panel.file.file.update', $model);
    }

    public function PanelDelete(User $user, File $model): bool
    {
        return $user->can('panel.file.file.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.file.file.list', File::class);
    }

    public function PanelChangeStatus(User $user, File $model): bool
    {
        return $user->can('panel.file.file.change_status', $model);
    }
}
