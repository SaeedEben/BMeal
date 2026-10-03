<?php

namespace App\Policies\Shopping;

use App\Models\Shopping\ShoppingList;
use App\Models\User\User;

class ShoppingListPolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.shopping.shopping_list.index', ShoppingList::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.shopping.shopping_list.store', ShoppingList::class);
    }

    public function PanelShow(User $user, ShoppingList $model): bool
    {
        return $user->can('panel.shopping.shopping_list.show', $model);
    }

    public function PanelUpdate(User $user, ShoppingList $model): bool
    {
        return $user->can('panel.shopping.shopping_list.update', $model);
    }

    public function PanelDelete(User $user, ShoppingList $model): bool
    {
        return $user->can('panel.shopping.shopping_list.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.shopping.shopping_list.list', ShoppingList::class);
    }

    public function PanelChangeStatus(User $user, ShoppingList $model): bool
    {
        return $user->can('panel.shopping.shopping_list.change_status', $model);
    }
}
