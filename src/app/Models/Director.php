<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Director extends Model
{
    use Filterable, HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function movies()
    {
        return $this->hasMany(Movie::class);
    }

}
