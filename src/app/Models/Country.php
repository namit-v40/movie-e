<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{

    protected $fillable = ['name', 'slug', 'code'];

    public function movies()
    {
        return $this->hasMany(Movie::class);
    }
}
