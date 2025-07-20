<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Filterable, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'user_identify',
        'name',
        'phone',
        'email_verified_at',
        'phone_verified_at',
        'avatar_img',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    protected $searchable = ['name', 'user_identify'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'guard' => 'user',
            'email_verified' => $this->hasVerifiedEmail(),
        ];
    }

    public function scopeDetail(Builder $query)
    {
        return $query->with([]);
    }

    public static function generateUserIdentifyByEmail($email)
    {
        $base = Str::slug(explode('@', $email)[0], '_');
        do {
            $identify = $base . '_' . rand(1000, 9999);
        } while (self::where('user_identify', $identify)->exists());

        return $identify;
    }
}
