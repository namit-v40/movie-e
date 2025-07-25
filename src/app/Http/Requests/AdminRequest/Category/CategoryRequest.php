<?php

namespace App\Http\Requests\AdminRequest\Category;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'slug' => 'nullable|string',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'search' => $this->search ?? null,
            'name' => $this->name ?? null,
            'slug' => $this->name ?? null,
        ]);
    }
}
