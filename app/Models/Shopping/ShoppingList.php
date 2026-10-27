<?php

namespace App\Models\Shopping;

use App\Enum\Common\StatusEnum;
use App\Models\User\User;
use Carbon\Carbon;
use Database\Factories\ShoppingListFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Symfony\Component\Uid\Uuid;

/**
 * @property Uuid|string $id
 * @property string $user_id
 * @property string $name
 * @property Carbon $week_date
 * @property StatusEnum|string $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relations ---------------------------------------
 * @property-read User          $user
 * @property-read Collection<int, ShoppingListItem> $items
 */
class ShoppingList extends Model
{
    /** @use HasFactory<ShoppingListFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'name',
        'status',
        'week_date',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusEnum::class,
        ];
    }

    // Relations ------------------------------------------------------------------------
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShoppingListItem::class);
    }
}
