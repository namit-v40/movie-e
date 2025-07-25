<?php

namespace App\Http\Requests\AdminRequest\Actor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class CreateActorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:actors',
            'avatar' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
        ];
    }
}
