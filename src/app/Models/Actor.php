<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Actor extends Model
{
    use Filterable, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function movies()
    {
        return $this->hasMany(Movie::class);
    }

}
