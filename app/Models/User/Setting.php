<?php

namespace App\Models\User;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Symfony\Component\Uid\Uuid;

/**
 * @property int $id
 * @property Uuid|string $user_id
 * @property bool $meal_reminder
 * @property bool $shopping_list_reminder
 * @property bool $weakly_planning_reminder
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relations ------------------------------------------
 * @property-read User $user
 */
class Setting extends Model
{
    protected $fillable = [
        'user_id',
        'meal_reminder',
        'shopping_list_reminder',
        'weakly_planning_reminder',
    ];

    protected function casts(): array
    {
        return [
            'meal_reminder' => 'boolean',
            'shopping_list_reminder' => 'boolean',
            'weakly_planning_reminder' => 'boolean',
        ];
    }

    // {Relations} --------------------------------------------
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // {Attributes} -------------------------------------------
    // {Methods} ----------------------------------------------
    // {Scopes} -----------------------------------------------
}
