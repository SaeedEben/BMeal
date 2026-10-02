<?php

namespace App\Models;

use App\Enum\Common\StatusEnum;
use Carbon\Carbon;
use Database\Factories\UserFavoriteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $user_id
 * @property string $recipe_id
 * @property Carbon|null $created_at
 * @property StatusEnum|string $status
 * @property-read User $user
 * @property-read Recipe $recipe
 */
class UserFavorite extends Model
{
    /** @use HasFactory<UserFavoriteFactory> */
    use HasFactory;

    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = null;

    protected $fillable = [
        'user_id',
        'recipe_id',
        'created_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'status' => StatusEnum::class,
        ];
    }

    // Relations ------------------------------------------------------------------------
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}
