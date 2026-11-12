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
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
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
            'mature_filter_preference' => ['nullable', 'string', 'in:blur,hide,show'],
            'show_mature_content' => ['nullable', 'boolean'],
            'hide_ai_content' => ['nullable', 'boolean'],
            'notify_new_artworks' => ['nullable', 'boolean'],
            'notify_messages' => ['nullable', 'boolean'],
            'social_links' => ['nullable', 'array'],
            'social_links.artstation' => ['nullable', 'url', 'max:255'],
            'social_links.instagram' => ['nullable', 'url', 'max:255'],
            'social_links.behance' => ['nullable', 'url', 'max:255'],
            'social_links.twitter' => ['nullable', 'url', 'max:255'],
            'social_links.linkedin' => ['nullable', 'url', 'max:255'],
            'social_links.website' => ['nullable', 'url', 'max:255'],
            'commission_services' => ['nullable', 'array', 'max:10'],
            'commission_services.*.title' => ['required_with:commission_services', 'string', 'max:100'],
            'commission_services.*.description' => ['nullable', 'string', 'max:500'],
            'commission_services.*.price_starting_at' => ['nullable', 'string', 'max:50'],
            'commission_services.*.currency' => ['nullable', 'string', 'max:10'],
            'commission_services.*.turnaround_time' => ['nullable', 'string', 'max:50'],
            'commission_services.*.is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('slug') && is_string($this->input('slug'))) {
            $this->merge([
                'slug' => strtolower(preg_replace('/[^a-z0-9_\-]/', '', $this->input('slug'))),
            ]);
        }

        if ($this->has('commission_services') && is_string($this->input('commission_services'))) {
            $decoded = json_decode($this->input('commission_services'), true);
            if (is_array($decoded)) {
                $clean = array_values(array_filter($decoded, fn($item) => !empty(trim($item['title'] ?? ''))));
                $this->merge(['commission_services' => array_slice($clean, 0, 10)]);
            }
        }

        $booleans = [
            'public_email_enabled',
            'show_mature_content',
            'hide_ai_content',
            'notify_new_artworks',
            'notify_messages',
        ];

        $merge = [];
        if ($this->has('mature_filter_preference')) {
            $merge['mature_filter_preference'] = $this->input('mature_filter_preference');
            // Keep show_mature_content synchronized for backward compatibility
            $merge['show_mature_content'] = ($this->input('mature_filter_preference') === 'show');
        }
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
