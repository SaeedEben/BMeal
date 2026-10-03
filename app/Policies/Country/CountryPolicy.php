<?php

namespace App\Policies\Country;

use App\Models\Country\Country;
use App\Models\User\User;

class CountryPolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.country.country.index', Country::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.country.country.store', Country::class);
    }

    public function PanelShow(User $user, Country $model): bool
    {
        return $user->can('panel.country.country.show', $model);
    }

    public function PanelUpdate(User $user, Country $model): bool
    {
        return $user->can('panel.country.country.update', $model);
    }

    public function PanelDelete(User $user, Country $model): bool
    {
        return $user->can('panel.country.country.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.country.country.list', Country::class);
    }

    public function PanelChangeStatus(User $user, Country $model): bool
    {
        return $user->can('panel.country.country.change_status', $model);
    }
}
