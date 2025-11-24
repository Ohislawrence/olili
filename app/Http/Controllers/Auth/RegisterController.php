<?php
// app/Http/Controllers/Auth/RegisterController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ExamBoard;
use Illuminate\Http\Request;
use App\Actions\Fortify\CreateNewUser;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use App\Services\SocialAuthService;

class RegisterController extends Controller
{

    protected $socialAuthService;

    public function __construct(SocialAuthService $socialAuthService)
    {
        $this->socialAuthService = $socialAuthService;
    }
    /**
     * Show the registration form.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Auth/Register', [
            'examBoards' => ExamBoard::active()
                ->orderBy('name')
                ->get(['id', 'name', 'code', 'country']),
            'hasTermsAndPrivacyPolicyFeature' => \Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature(),
            'availableProviders' => $this->socialAuthService->getAvailableProviders(),
        ]);
    }
    /**
    public function store(Request $request): RedirectResponse
    {
        $user = $this->createNewUser->create($request->all());

        auth()->login($user);

        return redirect()->route($user->getRoleNames()->first() . '.dashboard');
    }
    */
}
