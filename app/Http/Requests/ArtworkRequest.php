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
            'status' => ['nullable', 'in:published,hidden'],
            'external_url' => ['required', 'url', 'max:2000'],
            'category' => ['required', 'in:image,video,3d,audio,doc'],
        ];

        return $rules;
    }

    /**
     * Custom messages
     */
    public function messages(): array
    {
        return [
            'external_url.required' => 'You must provide a URL for your artwork.',
            'external_url.url' => 'The provided URL is not valid.',
            'title.required' => 'The artwork title is required.',
            'category.required' => 'Please select a category for your artwork.',
            'category.in' => 'The selected category is invalid.',
        ];
    }
}
