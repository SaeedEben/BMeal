<?php

namespace App\Models\Recipe;

use App\Enum\Common\StatusEnum;
use Carbon\Carbon;
use Database\Factories\RecipeStepsFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Symfony\Component\Uid\Uuid;

/**
 * @property Uuid|string $id
 * @property string $recipe_id
 * @property int $step_number
 * @property string $instruction
 * @property string|null $image
 * @property StatusEnum|string $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Recipe $recipe
 */
class RecipeSteps extends Model
{
    /** @use HasFactory<RecipeStepsFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'recipe_id',
        'step_number',
        'instruction',
        'image',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'step_number' => 'integer',
            'status' => StatusEnum::class,
        ];
    }

    // Relations ------------------------------------------------------------------------
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}
