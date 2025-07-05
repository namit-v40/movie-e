<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Year extends Model
{
    use Filterable;

    protected $fillable = ['name'];

    public function movies()
    {
        return $this->hasMany(Movie::class);
    }

}
