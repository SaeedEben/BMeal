<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enum\Common\StatusEnum;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Symfony\Component\Uid\Uuid;

/**
 * @property Uuid|string $id
 * @property string $full_name
 * @property string $status
 * @property string $email
 * @property string|null $email_verified_at
 * @property-read string       $password
 * @property string|null $remember_token
 * @property Carbon|null $last_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, HasUuids, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'password',
        'last_active',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => StatusEnum::class,
        ];
    }

    // {Relations} --------------------------------------------
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'model_has_roles',
            'model_id',
            'role_id',
            'id',
            'uuid'
        )->where('model_type', self::class);
    }

    // {Attributes} -------------------------------------------
    // {Methods} ----------------------------------------------
    public function assignRole(Role $role): void
    {
        $this->roles()->sync([
            $role->uuid => [
                'model_type' => self::class,
            ],
        ]);
    }

    public function getPermissionsByRole(): Collection
    {
        return $this->roles
            ->flatMap(fn ($role) => $role->permissions)
            ->unique();
    }

    // {Scopes} -----------------------------------------------
}
