<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OnboardingStep2Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Prepare inputs before validation.
     */
    protected function prepareForValidation(): void
    {
        // Parse skills if string or json
        if (is_string($this->skills)) {
            $decoded = json_decode($this->skills, true);
            $this->merge([
                'skills' => is_array($decoded)
                    ? $decoded
                    : array_values(array_filter(array_map('trim', explode(',', $this->skills))))
            ]);
        }

        // Parse software if string or json
        if (is_string($this->software)) {
            $decoded = json_decode($this->software, true);
            $this->merge([
                'software' => is_array($decoded)
                    ? $decoded
                    : array_values(array_filter(array_map('trim', explode(',', $this->software))))
            ]);
        }

        $this->merge([
            'public_email_enabled' => $this->boolean('public_email_enabled'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'slug' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[a-zA-Z0-9_\-]+$/',
                Rule::unique('users', 'slug')->ignore($userId),
            ],
            'bio' => ['required', 'string', 'min:20', 'max:1000'],
            'skills' => ['required', 'array', 'min:1', 'max:15'],
            'skills.*' => ['string', 'max:50'],
            'software' => ['nullable', 'array', 'max:15'],
            'software.*' => ['string', 'max:50'],
            'commission_status' => ['required', 'in:open,closed,consult'],
            'hiring_types' => ['nullable', 'array'],
            'hiring_types.*' => ['in:full-time,contract,freelance'],
            'public_email_enabled' => ['boolean'],
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'slug.required' => 'A unique public username / profile handle is required.',
            'slug.unique' => 'This username is already taken. Please choose another.',
            'slug.regex' => 'Username may only contain letters, numbers, underscores, and dashes.',
            'bio.required' => 'Please provide a professional summary or bio (at least 20 characters).',
            'bio.min' => 'Your bio should be at least 20 characters long.',
            'skills.required' => 'Please specify at least one professional skill or discipline.',
            'skills.min' => 'Please specify at least one professional skill or discipline.',
            'commission_status.required' => 'Please indicate your current commission availability.',
        ];
    }
}
