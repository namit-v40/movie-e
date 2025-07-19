<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Actor extends Model
{
    use Filterable, SoftDeletes;

    protected $fillable = ['name', 'avatar', 'bio'];

    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'movie_actor');
    }
}
