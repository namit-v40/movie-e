<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'slug'];

    public function movies()
    {
        return $this->hasMany(Movie::class);
    }

    /**
     * Scope for searching by name or code
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%');
            });
        }
        return $query;
    }

    /**
     * Scope for filtering by name or code
     */
    public function scopeFilter($query, array $filters)
    {
        if (isset($filters['name']) && $filters['name']) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (isset($filters['code']) && $filters['code']) {
            $query->where('code', 'like', '%' . $filters['code'] . '%');
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
            if (in_array($column, ['name', 'code', 'created_at'])) {
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