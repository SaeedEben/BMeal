<?php

namespace App\Policies\File;

use App\Models\File\File;
use App\Models\User\User;

class FilePolicy
{
    public function PanelStore(User $user): bool
    {
        return $user->can('panel.file.file.store', File::class);
    }

    public function PanelShow(User $user, File $model): bool
    {
        return $user->can('panel.file.file.show', $model);
    }

    public function PanelDelete(User $user, File $model): bool
    {
        return $user->can('panel.file.file.destroy', $model);
    }

}
