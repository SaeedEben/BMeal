<?php

namespace App\Models;

use App\Enum\Common\StatusEnum;
use Carbon\Carbon;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Symfony\Component\Uid\Uuid;

/**
 * @property Uuid|string $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property StatusEnum|string $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<int, Recipe> $recipes
 */
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'description',
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
        return $this->belongsToMany(Recipe::class, 'recipe_categories')->withPivot('status');
    }
}
