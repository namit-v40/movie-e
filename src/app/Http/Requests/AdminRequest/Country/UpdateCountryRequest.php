<?php

namespace App\Http\Requests\AdminRequest\Country;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UpdateCountryRequest extends FormRequest
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
                Rule::unique('countries', 'slug')->ignore($this->user()->id),
            ],
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('countries', 'code')->ignore($this->user()->id),
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
