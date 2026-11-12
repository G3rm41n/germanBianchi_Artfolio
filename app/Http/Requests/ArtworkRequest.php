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
            'subcategory' => ['nullable', 'string', 'max:100'],
            'is_ai_generated' => ['nullable', 'boolean'],
            'is_mature' => ['nullable', 'boolean'],
            'software_used' => ['nullable', 'array', 'max:15'],
            'software_used.*' => ['string', 'max:50'],
            'license' => ['nullable', 'string', 'in:all_rights_reserved,cc_by,cc_by_sa,cc_by_nc,cc_by_nc_nd,cc0'],
            'tags' => ['nullable', 'array', 'max:15'],
            'tags.*' => ['string', 'max:50'],
        ];

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('tags') && is_string($this->input('tags'))) {
            $raw = array_filter(array_map('trim', explode(',', $this->input('tags'))));
            $this->merge(['tags' => array_values($raw)]);
        }

        if ($this->has('software_used') && is_string($this->input('software_used'))) {
            $rawSoftware = array_filter(array_map('trim', explode(',', $this->input('software_used'))));
            $this->merge(['software_used' => array_values($rawSoftware)]);
        }

        $this->merge([
            'is_ai_generated' => $this->boolean('is_ai_generated'),
            'is_mature' => $this->boolean('is_mature'),
            'license' => $this->input('license') ?: 'all_rights_reserved',
        ]);
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
