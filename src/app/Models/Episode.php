<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Episode extends Model
{
    use Filterable, HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'name',
        'slug',
        'link_embed',
        'link_m3u8',
        'sort_order',
        'user_id',
        'movie_id',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public static function findEpisode($movie_id)
    {
        return Episode::where(function ($query) use ($movie_id) {
                $query->where('movie_id', $movie_id);
            });
    }

}
