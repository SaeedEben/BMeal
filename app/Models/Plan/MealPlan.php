<?php

namespace App\Models\Plan;

use App\Enum\Common\StatusEnum;
use Carbon\Carbon;
use Database\Factories\MealPlanFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Symfony\Component\Uid\Uuid;
use App\Models\User\User;

/**
 * @property Uuid|string $id
 * @property string $user_id
 * @property Carbon $week_start_date
 * @property StatusEnum|string $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User $user
 * @property-read Collection<int, MealPlanItem> $items
 */
class MealPlan extends Model
{
    /** @use HasFactory<MealPlanFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'week_start_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'week_start_date' => 'date',
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
        return $this->hasMany(MealPlanItem::class);
    }
}
