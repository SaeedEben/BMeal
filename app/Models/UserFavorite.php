<?php

namespace App\Models;

use Database\Factories\UserFavoriteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFavorite extends Model
{
    /** @use HasFactory<UserFavoriteFactory> */
    use HasFactory;
}
