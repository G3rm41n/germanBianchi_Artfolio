<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ArtworkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // We will use auth middleware and policies
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price_hint' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'in:public,hidden'],
        ];

        if ($this->isMethod('post')) {
            // Creation
            $rules['external_url'] = ['required', 'url', 'max:2000'];
        }

        return $rules;
    }

    /**
     * Custom messages
     */
    public function messages(): array
    {
        return [
            'external_url.required' => 'Debes proporcionar un enlace a tu obra.',
            'external_url.url' => 'El enlace proporcionado no tiene un formato válido.',
            'title.required' => 'El título de la obra es obligatorio.',
        ];
    }
}
