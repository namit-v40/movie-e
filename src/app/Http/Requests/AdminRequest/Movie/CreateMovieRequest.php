<?php

namespace App\Http\Requests\AdminRequest\Movie;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class CreateMovieRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string',
            'slug' => 'required|string|unique:movies,slug',
            'original_name' => 'required|string',
            'description' => 'nullable|string',
            'poster' => 'nullable|string',
            'thumbnail' => 'nullable|string',
            'release_year' => 'required|integer',
            'duration' => 'nullable|integer',
            'episode_current' => 'required|string',
            'episode_total' => 'required|string',
            'quality' => 'required|string',
            'country_id' => 'required|exists:countries,id',
            'type_id' => 'required|exists:types,id',
            'director_id' => 'required|exists:directors,id',
        ];
    }
}
