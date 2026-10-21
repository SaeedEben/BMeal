<?php

namespace App\Models\Recipe;

use App\Enum\Common\StatusEnum;
use App\Models\File\File;
use App\Models\Shopping\ShoppingListItem;
use Carbon\Carbon;
use Database\Factories\Recipe\IngredientFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Symfony\Component\Uid\Uuid;

/**
 * @property Uuid|string $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $image_id
 * @property StatusEnum|string $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relations -----------------------------------
 * @property-read Collection<int, RecipeIngredients> $recipeIngredients
 * @property-read Collection<int, ShoppingListItem> $shoppingListItems
 * @property File $image
 */
class Ingredient extends Model
{
    /** @use HasFactory<IngredientFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'image_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusEnum::class,
        ];
    }

    // Relations ------------------------------------------------------------------------
    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(RecipeIngredients::class);
    }

    public function shoppingListItems(): HasMany
    {
        return $this->hasMany(ShoppingListItem::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(File::class, 'image_id');
    }
}
