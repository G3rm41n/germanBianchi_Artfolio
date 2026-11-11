<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class OnboardingLayout extends Component
{
    public function __construct(
        public int $currentStep = 1,
        public string $title = 'Profile Setup'
    ) {}

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.onboarding');
    }
}
