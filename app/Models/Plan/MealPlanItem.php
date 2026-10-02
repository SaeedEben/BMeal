<?php

namespace App\Models\Plan;

use App\Enum\Common\StatusEnum;
use Carbon\Carbon;
use Database\Factories\MealPlanItemFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Symfony\Component\Uid\Uuid;
use App\Models\Recipe\Recipe;

/**
 * @property Uuid|string $id
 * @property int $meal_plan_id
 * @property string $recipe_id
 * @property Carbon $date
 * @property string $meal_type_id
 * @property int|null $servings
 * @property StatusEnum|string $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read MealPlan $mealPlan
 * @property-read Recipe $recipe
 */
class MealPlanItem extends Model
{
    /** @use HasFactory<MealPlanItemFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'meal_plan_id',
        'recipe_id',
        'date',
        'meal_type_id',
        'servings',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'servings' => 'integer',
            'status' => StatusEnum::class,
        ];
    }

    // Relations ------------------------------------------------------------------------
    public function mealPlan(): BelongsTo
    {
        return $this->belongsTo(MealPlan::class);
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}
