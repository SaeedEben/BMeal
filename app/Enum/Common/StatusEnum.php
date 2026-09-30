<?php

namespace App\Enum\Common;

enum StatusEnum: string
{
    case ACTIVE = 'active';
    case NOT_ACTIVE = 'not_active';

    public function label(): string
    {
        return __("enums.status.{$this->value}");
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
