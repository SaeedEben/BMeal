<?php

namespace App\Enum\MealType;

enum TypeEnum: string
{
    case BREAKFAST = 'brekfast';
    case LUNCH     = 'lunch';
    case DINNER    = 'dinner';
    case DESSERT   = 'dessert';
    case SNACK     = 'snack';
    case DRINK     = 'drink';
    case APPETIZER = 'appetizer';
    case SIDE_DISH = 'side_dish';

    public function label(): string
    {
        return __("enums.meal_type.types.{$this->value}");
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
