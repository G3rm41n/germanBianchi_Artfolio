<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()?->id),
            ],
            'slug' => [
                'nullable',
                'string',
                'min:3',
                'max:50',
                'regex:/^[a-z0-9_\-]+$/',
                Rule::unique(User::class)->ignore($this->user()?->id),
            ],
            'avatar_url' => ['nullable', 'url', 'max:2048'],
            'crop_x' => ['nullable', 'numeric'],
            'crop_y' => ['nullable', 'numeric'],
            'crop_zoom' => ['nullable', 'numeric', 'min:0.1', 'max:5'],
            'banner_url' => ['nullable', 'url', 'max:2048'],
            'banner_crop_x' => ['nullable', 'numeric'],
            'banner_crop_y' => ['nullable', 'numeric'],
            'banner_crop_zoom' => ['nullable', 'numeric', 'min:0.1', 'max:5'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'commission_status' => ['nullable', 'string', 'in:open,closed,consult'],
            'headline' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'skills' => ['nullable', 'array', 'max:15'],
            'skills.*' => ['string', 'max:50'],
            'software' => ['nullable', 'array', 'max:15'],
            'software.*' => ['string', 'max:50'],
            'hiring_types' => ['nullable', 'array'],
            'hiring_types.*' => ['in:full-time,contract,freelance'],
            'public_email_enabled' => ['nullable', 'boolean'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'show_mature_content' => ['nullable', 'boolean'],
            'hide_ai_content' => ['nullable', 'boolean'],
            'notify_new_artworks' => ['nullable', 'boolean'],
            'notify_messages' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('slug') && is_string($this->input('slug'))) {
            $this->merge([
                'slug' => strtolower(preg_replace('/[^a-z0-9_\-]/', '', $this->input('slug'))),
            ]);
        }

        $booleans = [
            'public_email_enabled',
            'show_mature_content',
            'hide_ai_content',
            'notify_new_artworks',
            'notify_messages',
        ];

        $merge = [];
        foreach ($booleans as $field) {
            if ($this->has($field)) {
                $merge[$field] = $this->boolean($field);
            }
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
    }
}
