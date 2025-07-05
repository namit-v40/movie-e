<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{

    protected $fillable = ['name', 'slug'];

    public function movies()
    {
        return $this->hasMany(Movie::class);
    }
}
