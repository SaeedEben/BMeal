<?php

namespace App\Models\Country;

use App\Enum\Common\StatusEnum;
use Carbon\Carbon;
use Database\Factories\CountryFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Symfony\Component\Uid\Uuid;
use App\Models\File\File;

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
 * @property-read File|null $flag
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
}
