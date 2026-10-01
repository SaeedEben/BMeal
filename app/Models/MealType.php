<?php

namespace App\Models;

use Database\Factories\MealTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealType extends Model
{
    /** @use HasFactory<MealTypeFactory> */
    use HasFactory;
}
