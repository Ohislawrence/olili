<?php
// app/Http/Controllers/Auth/SocialAuthController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SocialAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            Log::error("Social redirect error for {$provider}: " . $e->getMessage());
            
            return redirect()->route('login')
                ->withErrors([
                    'email' => $this->getUserFriendlyError($e->getMessage(), $provider)
                ]);
        }
    }

    public function callback(string $provider, Request $request)
    {
        try {
            $user = $this->socialAuthService->handleCallback($provider, $request->all());
            
            Auth::login($user, true);

            // Record login history
            if (method_exists($user, 'recordLogin')) {
                $user->recordLogin('social_' . $provider);
            }

            // Flash success message
            session()->flash('status', "Successfully signed in with " . ucfirst($provider));

            return redirect()->intended(route('dashboard'));

        } catch (\Exception $e) {
            Log::error("Social callback error for {$provider}: " . $e->getMessage());
            
            return redirect()->route('login')
                ->withErrors([
                    'email' => $this->getUserFriendlyError($e->getMessage(), $provider)
                ]);
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
            
            return back()->with('status', ucfirst($provider) . ' account unlinked successfully.');
        }
        
        return back()->withErrors(['email' => 'No linked account found for ' . ucfirst($provider)]);
    }

    /**
     * Get user-friendly error messages
     */
    protected function getUserFriendlyError(string $error, string $provider): string
    {
        if (str_contains($error, 'configuration')) {
            return 'Social login is not properly configured. Please contact support.';
        }

        if (str_contains($error, 'email') && str_contains($error, 'required')) {
            return ucfirst($provider) . ' did not provide your email address. Please use another login method.';
        }

        if (str_contains($error, 'state') || str_contains($error, 'session')) {
            return 'Login session expired. Please try again.';
        }

        return 'Failed to authenticate with ' . ucfirst($provider) . '. Please try again.';
    }
}