<?php

namespace App\Models;

use App\Enum\Common\StatusEnum;
use Database\Factories\RecipeMealTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $recipe_id
 * @property string $meal_type_id
 * @property StatusEnum|string $status
 * @property-read Recipe $recipe
 * @property-read MealType $mealType
 */
class RecipeMealType extends Model
{
    /** @use HasFactory<RecipeMealTypeFactory> */
    use HasFactory;

    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = null;

    protected $fillable = [
        'recipe_id',
        'meal_type_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusEnum::class,
        ];
    }

    // Relations ------------------------------------------------------------------------
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function mealType(): BelongsTo
    {
        return $this->belongsTo(MealType::class);
    }
}
