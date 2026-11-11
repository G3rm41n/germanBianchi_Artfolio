<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OnboardingStep1Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'headline'   => ['required', 'string', 'max:100'],
            'city'       => ['nullable', 'string', 'max:100'],
            'country'    => ['required', 'string', 'max:100'],
            'avatar_url' => ['nullable', 'url', 'max:2048'],
            'crop_x'     => ['nullable', 'numeric', 'between:-100,100'],
            'crop_y'     => ['nullable', 'numeric', 'between:-100,100'],
            'crop_zoom'  => ['nullable', 'numeric', 'between:0.5,3.0'],
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'headline.required' => 'Please provide a professional headline or job title.',
            'country.required'  => 'Please select your country of residence.',
            'avatar_url.url'    => 'Avatar must be a valid web image URL.',
        ];
    }
}
