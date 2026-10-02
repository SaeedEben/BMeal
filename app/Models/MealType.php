<?php

namespace App\Models;

use App\Enum\Common\StatusEnum;
use Carbon\Carbon;
use Database\Factories\MealTypeFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Symfony\Component\Uid\Uuid;

/**
 * @property Uuid|string $id
 * @property string $name
 * @property string $slug
 * @property StatusEnum|string $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<int, Recipe> $recipes
 * @property-read Collection<int, MealPlanItem> $mealPlanItems
 */
class MealType extends Model
{
    /** @use HasFactory<MealTypeFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusEnum::class,
        ];
    }

    // Relations ------------------------------------------------------------------------
    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'recipe_meal_types')->withPivot('status');
    }

    public function mealPlanItems(): HasMany
    {
        return $this->hasMany(MealPlanItem::class);
    }
}
