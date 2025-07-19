<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Type extends Model
{
    use Filterable, HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug'];

    public function movies()
    {
        return $this->hasMany(Movie::class);
    }
}
