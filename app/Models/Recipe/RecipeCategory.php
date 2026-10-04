<?php

namespace App\Models\Recipe;

use App\Enum\Common\StatusEnum;
use Database\Factories\Recipe\RecipeCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $recipe_id
 * @property string $category_id
 * @property StatusEnum|string $status
 * @property-read Recipe $recipe
 * @property-read Category $category
 */
class RecipeCategory extends Model
{
    /** @use HasFactory<RecipeCategoryFactory> */
    use HasFactory;

    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = null;

    protected $fillable = [
        'recipe_id',
        'category_id',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
