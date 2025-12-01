<?php
// app/Http/Controllers/Auth/SocialAuthController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SocialAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SocialAuthController extends Controller
{
    protected $socialAuthService;

    public function __construct(SocialAuthService $socialAuthService)
    {
        $this->socialAuthService = $socialAuthService;
    }

    public function redirect(string $provider)
    {
        try {
            return $this->socialAuthService->redirect($provider);
        } catch (\Exception $e) {
            Log::error("Social redirect error: " . $e->getMessage());
            return Inertia::location(route('login'))->with('error', 'Failed to initiate social login.');
        }
    }

    public function callback(string $provider, Request $request)
    {
        try {
            // Check for error parameters from provider
            if ($request->has('error')) {
                $error = $request->get('error');
                $errorDescription = $request->get('error_description', 'Unknown error');

                Log::error("OAuth error from {$provider}", [
                    'error' => $error,
                    'error_description' => $errorDescription,
                ]);

                return redirect()->route('login')
                    ->with('error', $this->getOAuthErrorDescription($error, $provider));
            }

            $user = $this->socialAuthService->handleCallback($provider, $request->all());

            Auth::login($user, true);

            // Record login history
            if (method_exists($user, 'recordLogin')) {
                $user->recordLogin('social_' . $provider);
            }

            // Check if this is a new user (just registered)
            $isNewUser = $user->wasRecentlyCreated;

            // Flash appropriate success message
            if ($isNewUser) {
                return Inertia::location(route('dashboard'))
                    ->with('success', 'Welcome! Your account has been created successfully with ' . ucfirst($provider) . '.');
            } else {
                return Inertia::location(route('dashboard'))
                    ->with('info', 'Successfully signed in with ' . ucfirst($provider));
            }

        } catch (\Exception $e) {
            Log::error("Social callback error for {$provider}: " . $e->getMessage());

            return redirect()->route('login')
                ->with('error', $this->getUserFriendlyError($e->getMessage(), $provider));
        }
    }

    public function unlinkAccount(string $provider)
    {
        $user = Auth::user();

        $socialAccount = $user->socialAccounts()->where('provider', $provider)->first();

        if ($socialAccount) {
            $socialAccount->delete();

            Log::info('Social account unlinked', [
                'user_id' => $user->id,
                'provider' => $provider,
            ]);

            return back()->with('success', ucfirst($provider) . ' account unlinked successfully.');
        }

        return back()->with('error', 'No linked account found for ' . ucfirst($provider));
    }

    /**
     * Get user-friendly OAuth error descriptions
     */
    protected function getOAuthErrorDescription(string $error, string $provider): string
    {
        $errorMessages = [
            'access_denied' => 'You denied access to your ' . ucfirst($provider) . ' account.',
            'invalid_scope' => 'The requested scope is invalid.',
            'unauthorized_client' => 'This application is not authorized to use ' . ucfirst($provider) . ' login.',
            'unsupported_response_type' => 'The response type is not supported.',
            'invalid_request' => 'The request was invalid.',
            'server_error' => ucfirst($provider) . ' encountered a server error. Please try again.',
            'temporarily_unavailable' => ucfirst($provider) . ' service is temporarily unavailable.',
        ];

        return $errorMessages[$error] ?? 'An error occurred during ' . ucfirst($provider) . ' authentication. Please try again.';
    }


    /**
     * Get user-friendly error messages
     */
    protected function getUserFriendlyError(string $error, string $provider): string
    {
        if (str_contains($error, 'No account found')) {
            return 'No account found with this email address. Please register first or use a different login method.';
        }

        if (str_contains($error, 'configuration')) {
            return 'Social login is not properly configured. Please contact support.';
        }

        if (str_contains($error, 'email') && str_contains($error, 'required')) {
            return ucfirst($provider) . ' did not provide your email address. Please use another login method.';
        }

        if (str_contains($error, 'state') || str_contains($error, 'session')) {
            return 'Login session expired. Please try again.';
        }

        if (str_contains($error, 'access token')) {
            return 'Failed to authenticate with ' . ucfirst($provider) . '. Please try again.';
        }

        return 'Failed to authenticate with ' . ucfirst($provider) . '. Please try again.';
    }

    /**
     * Link a social account to the current user
     */
    public function linkAccount(string $provider, Request $request)
    {
        try {
            $user = Auth::user();

            // Check for error parameters from provider
            if ($request->has('error')) {
                $error = $request->get('error');
                return back()->with('error', $this->getOAuthErrorDescription($error, $provider));
            }

            // Get access token and user info
            $tokenData = $this->socialAuthService->getAccessToken($provider, $request->get('code'));
            $userData = $this->socialAuthService->getUserInfo($provider, $tokenData['access_token']);

            // Link the account
            $this->socialAuthService->linkAccount($user, $provider, $userData, $tokenData);

            return back()->with('success', ucfirst($provider) . ' account linked successfully!');

        } catch (\Exception $e) {
            Log::error("Social account linking error for {$provider}: " . $e->getMessage());

            return back()->with('error', $this->getUserFriendlyError($e->getMessage(), $provider));
        }
    }
}
