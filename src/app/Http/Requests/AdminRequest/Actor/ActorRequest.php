<?php

namespace App\Http\Requests\AdminRequest\Actor;

use Illuminate\Foundation\Http\FormRequest;

class ActorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'sort' => 'nullable|string',
            'page' => 'nullable|integer',
            'search' => 'nullable|string',
            'per_page' => 'nullable|integer',
            'name' => 'nullable|string',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'search' => $this->search ?? null,
            'name' => $this->name ?? null,
        ]);
    }
}
