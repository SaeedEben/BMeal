<?php

namespace App\Enum\Recipe;

enum UnitTypeEnum: string
{
    case WEIGHT     = 'weight';
    case VOLUME     = 'volume';
    case COUNT      = 'count';
    case LENGTH     = 'length';
    case TEMPRATURE = 'temperature';
    case SIZE       = 'size';

    public function label(): string
    {
        return __("enums.recipe.difficulty.{$this->value}");
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
