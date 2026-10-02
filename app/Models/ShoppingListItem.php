<?php

namespace App\Models;

use App\Enum\Common\StatusEnum;
use Carbon\Carbon;
use Database\Factories\ShoppingListItemFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Symfony\Component\Uid\Uuid;

/**
 * @property Uuid|string $id
 * @property int $shopping_list_id
 * @property string|null $ingredient_id
 * @property string $name
 * @property string|null $quantity
 * @property string|null $unit
 * @property bool|null $is_completed
 * @property StatusEnum|string $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read ShoppingList $shoppingList
 * @property-read Ingredient|null $ingredient
 */
class ShoppingListItem extends Model
{
    /** @use HasFactory<ShoppingListItemFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'shopping_list_id',
        'ingredient_id',
        'name',
        'quantity',
        'unit',
        'is_completed',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'is_completed' => 'boolean',
            'status' => StatusEnum::class,
        ];
    }

    // Relations ------------------------------------------------------------------------
    public function shoppingList(): BelongsTo
    {
        return $this->belongsTo(ShoppingList::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
