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
        'name',
        'email_verified_at',
        'phone',
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
            'role' => $this->role,
            'email_verified' => $this->hasVerifiedEmail(),
        ];
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function creatorRequest()
    {
        return $this->hasOne(CreatorRequest::class, 'user_id');
    }

    public function scopeDetail(Builder $query)
    {
        return $query->with([
            'posts',
            'posts.bookmarks',
            'posts.reactions',
            'posts.reactions.user',
            'posts.comments',
            'posts.creator',
            'posts.tags',
            'followings',
            'followers',
            'bookmarks',
            'creatorRequest',
        ]);
    }

    public static function generateUserIdentifyByEmail($email)
    {
        $base = Str::slug(explode('@', $email)[0], '_');
        do {
            $identify = $base . '_' . rand(1000, 9999);
        } while (self::where('user_identify', $identify)->exists());

        return $identify;
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'creator_id');
    }

    public function bookmarks()
    {
        return $this->hasMany(PostBookmark::class);
    }

    public function followings()
    {
        return $this->hasMany(Follow::class, 'following_id');
    }

    public function followers()
    {
        return $this->hasMany(Follow::class, 'follower_id');
    }
}
