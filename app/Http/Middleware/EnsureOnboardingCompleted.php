<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingCompleted
{
    /**
     * Whitelisted route names that an un-onboarded user can access.
     */
    protected array $whitelistedRouteNames = [
        'onboarding.step',
        'onboarding.step1.save',
        'onboarding.step2.save',
        'onboarding.complete',
        'onboarding.checkUsername',
        'logout',
        'verification.notice',
        'verification.verify',
        'verification.send',
        'session.keepalive',
        'session.status',
    ];

    /**
     * Whitelisted URL paths / patterns.
     */
    protected array $whitelistedPatterns = [
        'onboarding/*',
        'logout',
        'email/verify*',
        'session/*',
        'up',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Allow guests and admin users to pass freely
        if (! $user || $user->is_admin) {
            return $next($request);
        }

        // If user already completed onboarding, proceed
        if ($user->isOnboarded()) {
            return $next($request);
        }

        // Check if current route is whitelisted by name
        $routeName = $request->route()?->getName();
        if ($routeName && in_array($routeName, $this->whitelistedRouteNames, true)) {
            return $next($request);
        }

        // Check if current URL matches any whitelisted pattern
        foreach ($this->whitelistedPatterns as $pattern) {
            if ($request->is($pattern)) {
                return $next($request);
            }
        }

        // JSON / AJAX requests from un-onboarded user
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Onboarding required before accessing this resource.',
                'onboarding_url' => route('onboarding.step', ['step' => $user->onboarding_step ?? 1]),
            ], 403);
        }

        // Redirect user to their current onboarding step
        $currentStep = min(3, max(1, (int) ($user->onboarding_step ?? 1)));

        return redirect()->route('onboarding.step', ['step' => $currentStep]);
    }
}
