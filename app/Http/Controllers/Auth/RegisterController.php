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

class RegisterController extends Controller
{
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
