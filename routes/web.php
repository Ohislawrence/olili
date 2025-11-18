<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LoginHistoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Test\TestingController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/pricing', function () {
    return Inertia::render('Pricing');
});

Route::get('/dashboard', function () {
    if (auth()->check()) {
        if (auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif (auth()->user()->hasRole('student')) {
            return redirect()->route('student.dashboard');
        }
    }
    return redirect()->route('login');
})->middleware(['auth'])->name('dashboard');

Route::get('/register', [RegisterController::class, 'create'])
    ->middleware(['guest'])
    ->name('register');

// another routes
require __DIR__.'/admin.php';
require __DIR__.'/student.php';
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/login-history', [LoginHistoryController::class, 'index'])->name('login.history');
});

// Test route
Route::get('/testing', [TestingController::class, 'test'])->name('test.testing');

// Paystack webhook
Route::post('/webhook/paystack', [PaymentController::class, 'handleWebhook'])->name('webhook.paystack');

// Payment routes
Route::prefix('payment')->name('payment.')->group(function () {
    // Pricing and subscription management
    Route::get('/pricing', [PaymentController::class, 'pricing'])->name('pricing');
    Route::get('/history', [PaymentController::class, 'history'])->name('history');
    Route::get('/pricing/{role}', [PaymentController::class, 'pricingForRole'])->name('pricing.role');

    // Payment initialization
    Route::post('/payment/subscription', [PaymentController::class, 'initializeSubscription'])->name('subscription.initialize');
    Route::post('/payment/one-time', [PaymentController::class, 'initializeOneTimePayment'])->name('one-time.initialize');

    // Payment callbacks
    Route::get('/payment/callback', [PaymentController::class, 'handleCallback'])->name('callback');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('success');
    Route::get('/payment/failure', [PaymentController::class, 'failure'])->name('failure');

    // Subscription management
    Route::post('/subscription/cancel', [PaymentController::class, 'cancelSubscription'])->name('subscription.cancel');
    Route::post('/subscription/change', [PaymentController::class, 'changePlan'])->name('subscription.change');

    // Payment history
    Route::get('/payment/history', [PaymentController::class, 'history'])->name('payment.history');
    Route::get('/payment/history/filter', [PaymentController::class, 'filterPaymentHistory'])->name('payment.history.filter');
    Route::get('/subscription/status', [PaymentController::class, 'subscriptionStatus'])->name('subscription.status');

});

