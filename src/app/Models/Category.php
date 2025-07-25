<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, Filterable;

    protected $fillable = ['name', 'slug'];

    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'movie_category');
    }

    /**
     * Scope for searching by name or slug
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }
        return $query;
    }

    /**
     * Scope for filtering by name
     */
    public function scopeFilter($query, array $filters)
    {
        if (isset($filters['name']) && $filters['name']) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (isset($filters['slug']) && $filters['slug']) {
            $query->where('slug', 'like', '%' . $filters['slug'] . '%');
        }

        return $query;
    }

    /**
     * Scope for sorting
     */
    public function scopeSort($query, $sort)
    {
        if ($sort) {
            $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
            $column = ltrim($sort, '-');
            if (in_array($column, ['name', 'created_at'])) {
                $query->orderBy($column, $direction);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    public function scopeDetail(Builder $query)
    {
        return $query;
    }
}
