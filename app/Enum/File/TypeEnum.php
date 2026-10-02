<?php

namespace App\Enum\File;

enum TypeEnum: string
{
    case PROFILE_PHOTO = 'profile_photo';
    case MEAL_PHOTO    = 'meal_photo';

    public function label(): string
    {
        return __("enums.file.type.{$this->value}");
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
