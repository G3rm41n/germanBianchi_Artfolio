<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class TurnstileRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!config('services.turnstile.enabled')) {
            return;
        }

        if (empty($value) || !is_string($value)) {
            $fail('Please complete the security challenge.');
            return;
        }

        $secret = config('services.turnstile.secret_key');

        try {
            $response = Http::asForm()->timeout(5)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secret,
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

            if (!$response->successful() || !$response->json('success')) {
                $fail('Security verification failed. Please try again.');
            }
        } catch (\Throwable $e) {
            report($e);
            $fail('Unable to verify security challenge at this time. Please try again.');
        }
    }
}

