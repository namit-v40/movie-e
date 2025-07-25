<?php

namespace App\Http\Requests\AdminRequest\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                Rule::unique('categories', 'slug')->ignore($this->user()->id),
            ],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'slug' => $this->slug ?? Str::slug($this->name),
        ]);
    }
}
