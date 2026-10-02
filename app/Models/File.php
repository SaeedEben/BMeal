<?php

namespace App\Models;

use Database\Factories\FileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Symfony\Component\Uid\Uuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Enum\File\StorageEnum;
use App\Enum\File\TypeEnum;
use Illuminate\Support\Facades\Storage;

/**
 * @property Uuid|string $id
 *
 * @property string      $name
 * @property string      $file_type
 * @property string|null $mime_type
 * @property int|null    $size
 * @property string      $storage_path
 * @property string      $storage_provider
 * @property string|null $external_url
 *
 * @property int         $user_id
 *
 * @property Carbon      $created_at
 * @property Carbon      $updated_at
 *
 * -------------------------------------- Relations
 * @property-read User   $user
 */
class File extends Model
{
     /** @use HasFactory<FileFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'file_type',
        'mime_type',
        'size',
        'storage_path',
        'storage_provider',
        'external_url',
    ];

    protected function casts() :array
    {
        return [
            'file_type'        => TypeEnum::class,
            'size'             => 'integer',
            'storage_provider' => StorageEnum::class,
        ];
    }

    // Relations ------------------------------------------------------------------------

    // Methods ------------------------------------------------------------------------

    /**
     * Filesystem disk this file is stored on.
     */
    public function disk(): string
    {
        return $this->storage_provider?->value ?? config('filesystems.default', 'local');
    }

    public function preview(): string
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk($this->disk());

        return $storage->url($this->storage_path);
    }

    // Attributes ------------------------------------------------------------------------
}
