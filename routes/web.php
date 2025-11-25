<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\FrontpageController;
use App\Http\Controllers\LoginHistoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Test\TestingController;
use App\Http\Controllers\WelcomeController;
use App\Mail\WelcomeStudentMail;
use Illuminate\Support\Facades\Mail;

// Public routes
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// Frontpage routes
Route::get('/features', [FrontpageController::class, 'features'])->name('features');
Route::get('/community', [FrontpageController::class, 'community'])->name('community');
Route::get('/about', [FrontpageController::class, 'about'])->name('about');
Route::get('/pricing', [FrontpageController::class, 'pricing'])->name('pricing');
Route::get('/help', [FrontpageController::class, 'help'])->name('help');
Route::get('/contact', [FrontpageController::class, 'contact'])->name('contact');
Route::get('/faq', [FrontpageController::class, 'faq'])->name('faq');

Route::get('/course', [FrontpageController::class, 'coursesIndex'])->name('courses.index');
Route::get('/course/{course:slug}', [FrontpageController::class, 'courseShow'])->name('courses.show');

Route::get('/enterprise', [FrontpageController::class, 'enterprise'])->name('enterprise');

// Blog routes (using FrontpagesController)
Route::get('/blog', [FrontpageController::class, 'blogIndex'])->name('blog.index');
Route::get('/blog/{slug}', [FrontpageController::class, 'blogShow'])->name('blog.show');

// Courses routes
Route::get('/courses', [FrontpageController::class, 'coursesIndex'])->name('courses.index');
Route::get('/courses/{id}', [FrontpageController::class, 'courseShow'])->name('courses.show');



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
    Route::get('/payment/history', [PaymentController::class, 'paymentHistory'])->name('payment.history');
    Route::get('/payment/history/filter', [PaymentController::class, 'filterPaymentHistory'])->name('payment.history.filter');
    Route::get('/subscription/status', [PaymentController::class, 'subscriptionStatus'])->name('subscription.status');

});

Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');

Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');
Route::post('/auth/{provider}/unlink', [SocialAuthController::class, 'unlinkAccount'])->name('social.unlink')->middleware(['auth']);
Route::post('/auth/{provider}/link', [SocialAuthController::class, 'linkAccount'])->name('social.link');

Route::get('/test-queue-email', function () {
    $user = App\Models\User::first();

    //\App\Mail\WelcomeStudentMail::dispatch($user);
    // or
    Mail::to($user->email)->queue(new WelcomeStudentMail($user));

    return "Email queued! Check your queue worker.";
});
