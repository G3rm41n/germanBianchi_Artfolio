<?php

namespace App\Http\Controllers;

use App\Http\Requests\OnboardingStep1Request;
use App\Http\Requests\OnboardingStep2Request;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    /**
     * Display a specific onboarding step.
     */
    public function step(Request $request, int $step): View|RedirectResponse
    {
        $user = $request->user();

        // If already completed onboarding, go to homepage
        if ($user->isOnboarded()) {
            return redirect()->route('home');
        }

        // Clamp step to valid range [1, 3]
        if ($step < 1 || $step > 3) {
            return redirect()->route('onboarding.step', ['step' => 1]);
        }

        $userStep = (int) ($user->onboarding_step ?? 1);

        // Guard step access progression:
        // Cannot access step 2 if step 1 hasn't been saved at least once
        if ($step === 2 && $userStep < 2) {
            return redirect()->route('onboarding.step', ['step' => 1])
                ->with('info', 'Please complete your personal details first.');
        }

        // Cannot access step 3 if step 2 hasn't been saved
        if ($step === 3 && $userStep < 3) {
            return redirect()->route('onboarding.step', ['step' => max(1, $userStep)])
                ->with('info', 'Please complete your professional profile first.');
        }

        return view("onboarding.step{$step}", [
            'user' => $user,
            'currentStep' => $step,
        ]);
    }

    /**
     * Save Step 1: Personal Details & Avatar.
     */
    public function saveStep1(OnboardingStep1Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validated();

        $user->fill([
            'headline'   => $validated['headline'],
            'city'       => $validated['city'] ?? null,
            'country'    => $validated['country'],
            'avatar_url' => $validated['avatar_url'] ?? null,
            'crop_x'     => $validated['crop_x'] ?? 0,
            'crop_y'     => $validated['crop_y'] ?? 0,
            'crop_zoom'  => $validated['crop_zoom'] ?? 1.0,
        ]);

        // Advance step if current step is 1
        if (($user->onboarding_step ?? 1) < 2) {
            $user->onboarding_step = 2;
        }

        $user->save();

        return redirect()->route('onboarding.step', ['step' => 2])
            ->with('status', 'Personal details saved! Next: set up your professional portfolio profile.');
    }

    /**
     * Save Step 2: Professional Profile.
     */
    public function saveStep2(OnboardingStep2Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validated();

        $user->fill([
            'slug'                 => $validated['slug'],
            'bio'                  => $validated['bio'],
            'skills'               => $validated['skills'],
            'software'             => $validated['software'] ?? [],
            'commission_status'    => $validated['commission_status'],
            'hiring_types'         => $validated['hiring_types'] ?? [],
            'public_email_enabled' => $validated['public_email_enabled'] ?? false,
        ]);

        $user->onboarding_step = 3;
        $user->save();

        return redirect()->route('onboarding.step', ['step' => 3])
            ->with('status', 'Professional profile saved successfully!');
    }

    /**
     * Step 3: Complete Onboarding & Launch to Homepage.
     */
    public function complete(Request $request): RedirectResponse
    {
        $user = $request->user();

        $user->forceFill([
            'onboarding_step'         => 3,
            'onboarding_completed_at' => now(),
        ])->save();

        return redirect()->route('home')
            ->with('status', 'Welcome to Artfolio! Your creator profile is now live.');
    }

    /**
     * AJAX endpoint to check slug/username availability in real time.
     */
    public function checkUsername(Request $request): JsonResponse
    {
        $slug = trim((string) $request->query('slug', ''));

        if (strlen($slug) < 3 || strlen($slug) > 50 || ! preg_match('/^[a-zA-Z0-9_\-]+$/', $slug)) {
            return response()->json([
                'available' => false,
                'message'   => 'Must be 3-50 chars (letters, numbers, dashes, underscores).',
            ]);
        }

        $user = $request->user();

        $exists = User::where('slug', $slug)
            ->when($user, fn ($q) => $q->where('id', '!=', $user->id))
            ->exists();

        return response()->json([
            'available' => ! $exists,
            'message'   => $exists ? 'This handle is already taken.' : 'Handle is available!',
            'slug'      => $slug,
        ]);
    }
}

