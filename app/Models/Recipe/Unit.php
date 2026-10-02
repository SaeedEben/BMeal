<?php

namespace App\Models\Recipe;

use App\Enum\Common\StatusEnum;
use Carbon\Carbon;
use Database\Factories\UnitFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Uid\Uuid;

/**
 * @property Uuid|string $id
 * @property string $name
 * @property string $symbol
 * @property string $slug
 * @property string $type
 * @property string|null $conversion_factor
 * @property bool $is_metric
 * @property StatusEnum|string $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Unit extends Model
{
    /** @use HasFactory<UnitFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'symbol',
        'slug',
        'type',
        'conversion_factor',
        'is_metric',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'conversion_factor' => 'decimal:6',
            'is_metric' => 'boolean',
            'status' => StatusEnum::class,
        ];
    }
}
