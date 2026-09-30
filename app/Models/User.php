<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * property             integer             $id
 * property             string              $full_name
 * property             string              $email
 * property             string|null         $email_verified_at
 * property-read        string              $password
 * property             string|null         $remember_token
 * property \Illuminate\Support\Carbon $created_at
 * property \Illuminate\Support\Carbon $updated_at
 */

#[Fillable([
    'full_name',
    'email',
    'password',
])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // {Relations} ----------------------------------------------
    // {Attributes} ----------------------------------------------
    // {Methods} ----------------------------------------------
    // {Scopes} ----------------------------------------------
}
