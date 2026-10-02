<?php

namespace App\Enum\File;

enum StorageEnum: string
{
    case LOCAL         = 'local';
    case PUBLIC        = 'public';
    case S3            = 's3';
    case MINIO         = 'minio';
    case CLOUDFLARE_R2 = 'r2';

    public function label(): string
    {
        return __("enums.file.storage.{$this->value}");
    }

    public static function values(): array
    {
        return array_map(
            fn (self $status) => $status->value,
            self::cases()
        );
    }

    public static function options(): array
    {
        return array_map(
            fn (self $status) => [
                'value' => $status->value,
                'label' => $status->label(),
            ],
            self::cases()
        );
    }
}
