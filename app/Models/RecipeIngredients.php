<?php

namespace App\Models;

use Database\Factories\RecipeIngredientsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipeIngredients extends Model
{
    /** @use HasFactory<RecipeIngredientsFactory> */
    use HasFactory;
}
