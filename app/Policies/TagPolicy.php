<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;

class TagPolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.tag.index', Tag::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.tag.store', Tag::class);
    }

    public function PanelShow(User $user, Tag $model): bool
    {
        return $user->can('panel.tag.show', $model);
    }

    public function PanelUpdate(User $user, Tag $model): bool
    {
        return $user->can('panel.tag.update', $model);
    }

    public function PanelDelete(User $user, Tag $model): bool
    {
        return $user->can('panel.tag.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.tag.list', Tag::class);
    }

    public function PanelChangeStatus(User $user, Tag $model): bool
    {
        return $user->can('panel.tag.change_status', $model);
    }
}
