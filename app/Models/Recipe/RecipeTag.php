<?php

namespace App\Models\Recipe;

use App\Enum\Common\StatusEnum;
use Database\Factories\RecipeTagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $recipe_id
 * @property string $tag_id
 * @property StatusEnum|string $status
 * @property-read Recipe $recipe
 * @property-read Tag $tag
 */
class RecipeTag extends Model
{
    /** @use HasFactory<RecipeTagFactory> */
    use HasFactory;

    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = null;

    protected $fillable = [
        'recipe_id',
        'tag_id',
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

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }
}
