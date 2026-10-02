<?php

namespace App\Models\Recipe;

use App\Enum\Common\StatusEnum;
use Carbon\Carbon;
use Database\Factories\RecipeFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Symfony\Component\Uid\Uuid;
use App\Models\Country\Country;
use App\Models\Recipe\RecipeSteps;
use App\Models\Recipe\RecipeIngredients;
use App\Models\Recipe\Tag;


/**
 * @property Uuid|string          $id
 * @property string               $country_id
 * @property string               $name
 * @property string               $slug
 * @property string|null          $description
 * @property string|null          $image
 * @property int|null             $prep_time_minutes
 * @property int|null             $cook_time_minutes
 * @property int|null             $servings
 * @property string|null          $difficulty
 * @property string|null          $calories_per_serving
 * @property StatusEnum|string    $status
 * @property Carbon               $created_at
 * @property Carbon               $updated_at
 * @property-read Country         $country
 * @property-read Collection<int, Ingredient> $ingredients
 * @property-read Collection<int, RecipeIngredients> $recipeIngredients
 * @property-read Collection<int, RecipeSteps> $steps
 * @property-read Collection<int, Category> $categories
 * @property-read Collection<int, Tag> $tags
 */
class Recipe extends Model
{
    /** @use HasFactory<RecipeFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'country_id',
        'name',
        'slug',
        'description',
        'image',
        'prep_time_minutes',
        'cook_time_minutes',
        'servings',
        'difficulty',
        'calories_per_serving',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'prep_time_minutes' => 'integer',
            'cook_time_minutes' => 'integer',
            'servings' => 'integer',
            'calories_per_serving' => 'decimal:2',
            'status' => StatusEnum::class,
        ];
    }

    // Relations ------------------------------------------------------------------------
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients')
            ->withPivot('id', 'unit_id', 'quantity', 'notes', 'sort_order', 'status')
            ->withTimestamps();
    }

    public function steps(): HasMany
    {
        return $this->hasMany(RecipeSteps::class);
    }

    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(RecipeIngredients::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'recipe_categories')->withPivot('status');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'recipe_tags')->withPivot('status');
    }
}
