<?php

namespace App\Policies;

use App\Models\ShoppingListItem;
use App\Models\User;

class ShoppingListItemPolicy
{
    public function PanelIndex(User $user): bool
    {
        return $user->can('panel.shopping_list_item.index', ShoppingListItem::class);
    }

    public function PanelStore(User $user): bool
    {
        return $user->can('panel.shopping_list_item.store', ShoppingListItem::class);
    }

    public function PanelShow(User $user, ShoppingListItem $model): bool
    {
        return $user->can('panel.shopping_list_item.show', $model);
    }

    public function PanelUpdate(User $user, ShoppingListItem $model): bool
    {
        return $user->can('panel.shopping_list_item.update', $model);
    }

    public function PanelDelete(User $user, ShoppingListItem $model): bool
    {
        return $user->can('panel.shopping_list_item.destroy', $model);
    }

    public function PanelList(User $user): bool
    {
        return $user->can('panel.shopping_list_item.list', ShoppingListItem::class);
    }

    public function PanelChangeStatus(User $user, ShoppingListItem $model): bool
    {
        return $user->can('panel.shopping_list_item.change_status', $model);
    }
}
