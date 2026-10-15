<?php

namespace App\Models\Recipe;

use App\Enum\Common\StatusEnum;
use Carbon\Carbon;
use Database\Factories\Recipe\RecipeIngredientsFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Symfony\Component\Uid\Uuid;
use Illuminate\Support\Str;

/**
 * @property Uuid|string $id
 * @property string $recipe_id
 * @property string $ingredient_id
 * @property string|null $unit_id
 * @property string|null $quantity
 * @property string|null $notes
 * @property int|null $sort_order
 * @property StatusEnum|string $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Recipe $recipe
 * @property-read Ingredient $ingredient
 * @property-read Unit|null $unit
 */
class RecipeIngredients extends Pivot
{
    /** @use HasFactory<RecipeIngredientsFactory> */
    use HasFactory, HasUuids;

    protected $table = 'recipe_ingredients';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'recipe_id',
        'ingredient_id',
        'unit_id',
        'quantity',
        'notes',
        'sort_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'sort_order' => 'integer',
            'status' => StatusEnum::class,
        ];
    }

     protected static function booted(): void
    {
        static::creating(function (RecipeIngredients $pivot) {
            if (empty($pivot->id)) {
                $pivot->id = (string) Str::uuid7();
            }
        });
    }

    // Relations ------------------------------------------------------------------------
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
