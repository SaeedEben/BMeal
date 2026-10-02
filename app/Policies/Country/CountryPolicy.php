<?php

namespace App\Policies\Country;

use App\Models\Country\Country;
use App\Models\User\User;

class CountryPolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.country.index', Country::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.country.store', Country::class);
    }

    public function PanelShow(User $user, Country $model): bool
    {
        return $user->can('panel.country.show', $model);
    }

    public function PanelUpdate(User $user, Country $model): bool
    {
        return $user->can('panel.country.update', $model);
    }

    public function PanelDelete(User $user, Country $model): bool
    {
        return $user->can('panel.country.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.country.list', Country::class);
    }

    public function PanelChangeStatus(User $user, Country $model): bool
    {
        return $user->can('panel.country.change_status', $model);
    }
}
