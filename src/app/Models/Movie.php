<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    use Filterable, HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'original_name',
        'year_id',
        'type_id',
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function Category()
    {
        return $this->hasMany(Category::class);
    }

    public function Country()
    {
        return $this->hasMany(Country::class);
    }

    public function Episode()
    {
        return $this->hasMany(Episode::class);
    }

    public function Actor()
    {
        return $this->hasMany(Actor::class);
    }

    public function Director()
    {
        return $this->hasMany(Director::class);
    }

    public function Year(): BelongsTo
    {
        return $this->belongsTo(Year::class, 'year_id');
    }

    public function Type(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function getMoviesByName($name)
    {
        return $this->byName($name)
            ->detail()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getMoviesBySlug($slug)
    {
        return $this->bySlug($slug)
            ->detail()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function subscriptions()
    {
        return $this->morphMany(Subscription::class, 'ownerable');
    }
}
