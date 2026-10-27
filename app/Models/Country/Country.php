<?php

namespace App\Models\Country;

use App\Enum\Common\StatusEnum;
use App\Models\File\File;
use App\Models\Recipe\Recipe;
use Carbon\Carbon;
use Database\Factories\Country\CountryFactory;
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
 * @property string $code
 * @property string $slug
 * @property string|null $description
 * @property string|null $flag_id
 * @property StatusEnum|string $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relations ---------------------------
 * @property-read File $flag
 * @property-read Collection<int, Recipe> $recipes
 */
class Country extends Model
{
    /** @use HasFactory<CountryFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'code',
        'slug',
        'description',
        'flag_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusEnum::class,
        ];
    }

    // Relations ------------------------------------------------------------------------
    public function flag(): BelongsTo
    {
        return $this->belongsTo(File::class, 'flag_id');
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }
}
