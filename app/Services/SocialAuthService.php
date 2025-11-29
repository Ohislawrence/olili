<?php
// app/Services/SocialAuthService.php
namespace App\Services;

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SocialAuthService
{
    protected $providers = [
        'google' => [
            'auth_url' => 'https://accounts.google.com/o/oauth2/auth',
            'token_url' => 'https://oauth2.googleapis.com/token',
            'user_url' => 'https://www.googleapis.com/oauth2/v3/userinfo',
            'scopes' => ['openid', 'email', 'profile'],
        ],
        'github' => [
            'auth_url' => 'https://github.com/login/oauth/authorize',
            'token_url' => 'https://github.com/login/oauth/access_token',
            'user_url' => 'https://api.github.com/user',
            'scopes' => ['user:email'],
            'user_agent' => 'Olilearn', // GitHub requires User-Agent
        ],
        'facebook' => [
            'auth_url' => 'https://www.facebook.com/v19.0/dialog/oauth',
            'token_url' => 'https://graph.facebook.com/v19.0/oauth/access_token',
            'user_url' => 'https://graph.facebook.com/v19.0/me',
            'scopes' => ['email', 'public_profile'],
            'fields' => 'id,name,email,first_name,last_name,picture',
        ],
    ];

    public function redirect(string $provider)
    {
        if (!isset($this->providers[$provider])) {
            abort(404, 'Unsupported provider');
        }

        // Validate configuration
        $this->validateProviderConfig($provider);

        $config = $this->providers[$provider];
        $clientId = config("services.{$provider}.client_id");
        $redirectUri = config("services.{$provider}.redirect");

        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => implode(' ', $config['scopes']),
            'response_type' => 'code',
            'state' => $this->generateState(),
            'access_type' => 'online',
        ];

        // Provider-specific parameters
        if ($provider === 'google') {
            $params['prompt'] = 'consent select_account';
        }

        if ($provider === 'github') {
            unset($params['scope']);
            $params['scope'] = 'user:email';
        }

        session([
            'oauth_state' => $params['state'],
            'oauth_provider' => $provider,
        ]);

        $url = $config['auth_url'] . '?' . http_build_query($params);

        Log::info("Social login redirect", ['provider' => $provider, 'url' => $url]);

        return redirect($url);
    }

    public function handleCallback(string $provider, array $requestData)
    {
        if (!isset($this->providers[$provider])) {
            throw new \Exception('Unsupported OAuth provider');
        }

        // Validate required parameters
        if (!isset($requestData['code'])) {
            throw new \Exception('Authorization code not provided');
        }

        // Verify state for CSRF protection
        $this->verifyState($requestData['state'] ?? '');

        // Get access token
        $tokenData = $this->getAccessToken($provider, $requestData['code']);

        // Get user info
        $userData = $this->getUserInfo($provider, $tokenData['access_token']);

        // Find existing user - don't create new ones
        return $this->findExistingUser($provider, $userData, $tokenData);
    }


    protected function getAccessToken(string $provider, string $code): array
    {
        $config = $this->providers[$provider];
        $clientId = config("services.{$provider}.client_id");
        $clientSecret = config("services.{$provider}.client_secret");
        $redirectUri = config("services.{$provider}.redirect");

        $params = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $code,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ];

        $http = Http::asForm();

        // GitHub returns data as x-www-form-urlencoded by default
        if ($provider === 'github') {
            $http = $http->withHeaders([
                'Accept' => 'application/json',
            ]);
        }

        $response = $http->post($config['token_url'], $params);

        if (!$response->successful()) {
            Log::error('OAuth token request failed', [
                'provider' => $provider,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            throw new \Exception('Failed to obtain access token');
        }

        $data = $response->json();

        if (!isset($data['access_token'])) {
            throw new \Exception('Access token not found in response');
        }

        return $data;
    }

    protected function getUserInfo(string $provider, string $token): array
    {
        $config = $this->providers[$provider];
        $url = $config['user_url'];

        $http = Http::withToken($token);

        // Add provider-specific headers and parameters
        if ($provider === 'github') {
            $http = $http->withHeaders([
                'User-Agent' => $config['user_agent'] ?? 'YourApp',
                'Accept' => 'application/vnd.github.v3+json',
            ]);
        }

        if ($provider === 'facebook') {
            $url .= '?fields=' . ($config['fields'] ?? 'id,name,email');
        }

        $response = $http->get($url);

        if (!$response->successful()) {
            Log::error('OAuth user info request failed', [
                'provider' => $provider,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            throw new \Exception('Failed to get user information');
        }

        $userData = $response->json();

        // Handle provider-specific data formatting
        return $this->formatUserData($provider, $userData);
    }

    protected function formatUserData(string $provider, array $data): array
    {
        switch ($provider) {
            case 'google':
                return [
                    'id' => $data['sub'],
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'avatar' => $data['picture'] ?? null,
                    'email_verified' => $data['email_verified'] ?? false,
                ];

            case 'github':
                // Get email if not provided in main response
                if (empty($data['email'])) {
                    $email = $this->getGitHubPrimaryEmail($data['access_token'] ?? '');
                    if ($email) {
                        $data['email'] = $email;
                    }
                }

                return [
                    'id' => $data['id'],
                    'name' => $data['name'] ?? $data['login'],
                    'email' => $data['email'],
                    'avatar' => $data['avatar_url'] ?? null,
                    'email_verified' => true, // GitHub emails are verified
                ];

            case 'facebook':
                return [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'email' => $data['email'] ?? null,
                    'avatar' => isset($data['picture']['data']['url']) ? $data['picture']['data']['url'] : null,
                    'email_verified' => true, // Assume verified for Facebook
                ];

            default:
                return $data;
        }
    }

    protected function getGitHubPrimaryEmail(string $token): ?string
    {
        try {
            $response = Http::withToken($token)
                ->withHeaders([
                    'User-Agent' => 'YourApp',
                    'Accept' => 'application/vnd.github.v3+json',
                ])
                ->get('https://api.github.com/user/emails');

            if ($response->successful()) {
                $emails = $response->json();
                foreach ($emails as $email) {
                    if ($email['primary'] && $email['verified']) {
                        return $email['email'];
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to fetch GitHub emails: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Find existing user - DO NOT create new users
     */
    protected function findExistingUser(string $provider, array $userData, array $tokenData)
    {
        // Validate required user data
        if (empty($userData['email'])) {
            throw new \Exception('Email address is required but not provided by the provider');
        }

        // First, check if social account exists
        $socialAccount = SocialAccount::where('provider', $provider)
            ->where('provider_id', $userData['id'])
            ->first();

        if ($socialAccount) {
            // Update token information for existing social account
            $socialAccount->update([
                'token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'] ?? null,
                'expires_at' => isset($tokenData['expires_in']) ? now()->addSeconds($tokenData['expires_in']) : null,
                'provider_data' => $userData,
            ]);

            Log::info('User signed in via existing social account', [
                'user_id' => $socialAccount->user_id,
                'provider' => $provider,
                'provider_id' => $userData['id'],
            ]);

            return $socialAccount->user;
        }

        // Check if user exists by email (but doesn't have this social account linked)
        $user = User::where('email', $userData['email'])->first();

        if ($user) {
            // User exists but social account is not linked - link it now
            SocialAccount::create([
                'user_id' => $user->id,
                'provider' => $provider,
                'provider_id' => $userData['id'],
                'token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'] ?? null,
                'expires_at' => isset($tokenData['expires_in']) ? now()->addSeconds($tokenData['expires_in']) : null,
                'provider_data' => $userData,
            ]);

            Auth::login($user);
            return redirect()->route('student.dashboard');

            //return $user;
        }

        // User doesn't exist - CREATE NEW USER with social data
        return $this->createUserFromSocialData($provider, $userData, $tokenData);
    }

    protected function createUserFromSocialData(string $provider, array $userData, array $tokenData)
    {
        try {
            // Prepare data for user creation
            $userInput = [
                'name' => $userData['name'] ?? 'Social User',
                'email' => $userData['email'],
                'provider' => $provider,
                'provider_id' => $userData['id'],
                'provider_token' => $tokenData['access_token'],
                'social_refresh_token' => $tokenData['refresh_token'] ?? null,
                'social_expires_in' => $tokenData['expires_in'] ?? null,
                'social_user_data' => $userData,
                // Set default role - you might want to redirect to role selection instead
                'role' => 'student',
            ];

            // Use Fortify's CreateNewUser action to create the user
            $createNewUser = app(\App\Actions\Fortify\CreateNewUser::class);
            $user = $createNewUser->create($userInput);

            Auth::login($user);

            return redirect()->route('student.dashboard');

            //return $user;

        } catch (\Exception $e) {
            Log::error('Failed to create user from social data', [
                'provider' => $provider,
                'email' => $userData['email'],
                'error' => $e->getMessage()
            ]);

            throw new \Exception('Failed to create user account: ' . $e->getMessage());
        }
    }
    protected function verifyState(string $state)
    {
        $sessionState = session('oauth_state');
        $sessionProvider = session('oauth_provider');

        if (!$sessionState || !$sessionProvider) {
            throw new \Exception('OAuth session expired');
        }

        if (!hash_equals($sessionState, $state)) {
            throw new \Exception('Invalid OAuth state parameter');
        }

        // Clear the state after verification
        session()->forget(['oauth_state', 'oauth_provider']);
    }

    protected function validateProviderConfig(string $provider)
    {
        $requiredConfig = ['client_id', 'client_secret', 'redirect'];

        foreach ($requiredConfig as $config) {
            if (empty(config("services.{$provider}.{$config}"))) {
                throw new \Exception("Missing configuration for {$provider}: {$config}");
            }
        }
    }

    protected function generateState(): string
    {
        return Str::random(40);
    }

    /**
     * Get available providers
     */
    public function getAvailableProviders(): array
    {
        $providers = [];

        foreach (array_keys($this->providers) as $provider) {
            try {
                $this->validateProviderConfig($provider);
                $providers[] = $provider;
            } catch (\Exception $e) {
                // Skip providers with missing configuration
                continue;
            }
        }

        return $providers;
    }

    /**
     * Link a social account to an existing user
     */
    public function linkAccount(User $user, string $provider, array $userData, array $tokenData): void
    {
        // Check if social account already exists for this provider
        $existingAccount = SocialAccount::where('provider', $provider)
            ->where('provider_id', $userData['id'])
            ->first();

        if ($existingAccount) {
            if ($existingAccount->user_id !== $user->id) {
                throw new \Exception('This social account is already linked to another user.');
            }
            // Update existing account
            $existingAccount->update([
                'token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'] ?? null,
                'expires_at' => isset($tokenData['expires_in']) ? now()->addSeconds($tokenData['expires_in']) : null,
                'provider_data' => $userData,
            ]);
        } else {
            // Create new social account link
            SocialAccount::create([
                'user_id' => $user->id,
                'provider' => $provider,
                'provider_id' => $userData['id'],
                'token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'] ?? null,
                'expires_at' => isset($tokenData['expires_in']) ? now()->addSeconds($tokenData['expires_in']) : null,
                'provider_data' => $userData,
            ]);
        }

        Log::info('Social account linked manually', [
            'user_id' => $user->id,
            'provider' => $provider,
            'provider_id' => $userData['id'],
        ]);
    }
}
