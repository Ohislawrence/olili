<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\FrontpageController;
use App\Http\Controllers\LoginHistoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Test\TestingController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\Community\CommunityController as FrontCommunityController;
use App\Http\Controllers\Community\PostController;
use App\Http\Controllers\Community\ProfileController;
use App\Http\Controllers\ContactController;
use App\Mail\WelcomeStudentMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Api\PushSubscriptionController;
use App\Http\Controllers\Student\CertificateController;

// Public routes
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// Frontpage routes
Route::get('/features', [FrontpageController::class, 'features'])->name('features');
Route::get('/about', [FrontpageController::class, 'about'])->name('about');
Route::get('/pricing', [FrontpageController::class, 'pricing'])->name('pricing');
Route::get('/help', [FrontpageController::class, 'help'])->name('help');
Route::get('/contact', [FrontpageController::class, 'contact'])->name('contact');
Route::get('/faq', [FrontpageController::class, 'faq'])->name('faq');

// Courses routes
Route::get('/course', [FrontpageController::class, 'coursesIndex'])->name('courses.index');
Route::get('/course/{id}/{slug?}', [FrontpageController::class, 'courseShow'])->name('courses.show');

Route::get('/enterprise', [FrontpageController::class, 'enterprise'])->name('enterprise');

// Blog routes (using FrontpagesController)
Route::get('/blog', [FrontpageController::class, 'blogIndex'])->name('blog.index');
Route::get('/blog/{slug}', [FrontpageController::class, 'blogShow'])->name('blog.show');

// Contact Form Routes
Route::post('/contact/submit', [ContactController::class, 'submitContactForm'])->name('contact.submit');
Route::post('/partnership/submit', [ContactController::class, 'submitPartnershipForm'])->name('partnership.submit');
Route::post('/newsletter/subscribe', [ContactController::class, 'subscribeNewsletter'])->name('newsletter.subscribe');

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



Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/login-history', [LoginHistoryController::class, 'index'])->name('login.history');
});

// Onboarding
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->prefix('onboarding')->name('onboarding.')->group(function () {
    Route::post('/complete', [OnboardingController::class, 'complete'])->name('complete');
    Route::post('/skip', [OnboardingController::class, 'skip'])->name('skip');
    Route::post('/restart', [OnboardingController::class, 'restart'])->name('restart');
    Route::get('/status', [OnboardingController::class, 'status'])->name('status');
});

// Community - Public routes
Route::prefix('community')->name('community.')->group(function () {
    // Main community page
    Route::get('/', [FrontCommunityController::class, 'index'])->name('index');

    // View individual post
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

    // View user profiles
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/{user}/followers', [ProfileController::class, 'followers'])->name('profile.followers');
    Route::get('/profile/{user}/following', [ProfileController::class, 'following'])->name('profile.following');

    //auth route
    Route::middleware([config('jetstream.auth_session'),'auth:sanctum','verified'])->group(function () {

        Route::get('/posts/create', [PostController::class, 'create'])->name('create');
        Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
        // Post interactions (edit/delete)
        Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
        Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

        // Like/Unlike
        Route::post('/posts/{post}/like', [PostController::class, 'like'])->name('posts.like');
        Route::delete('/posts/{post}/like', [PostController::class, 'unlike'])->name('posts.unlike');

        // Comments
        Route::post('/posts/{post}/comments', [PostController::class, 'storeComment'])->name('posts.comments.store');
        Route::delete('/comments/{comment}', [PostController::class, 'deleteComment'])->name('comments.destroy');

        // Follow/Unfollow
        Route::post('/profile/{user}/follow', [ProfileController::class, 'follow'])->name('profile.follow');
        Route::delete('/profile/{user}/follow', [ProfileController::class, 'unfollow'])->name('profile.unfollow');
    });
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
    Route::post('/subscription', [PaymentController::class, 'initializeSubscription'])->name('subscription.initialize');
    Route::post('/one-time', [PaymentController::class, 'initializeOneTimePayment'])->name('one-time.initialize');

    // Payment callbacks
    Route::get('/callback', [PaymentController::class, 'handleCallback'])->name('callback');
    Route::get('/success', [PaymentController::class, 'success'])->name('success');
    Route::get('/failure', [PaymentController::class, 'failure'])->name('failure');

    // Subscription management
    Route::post('/subscription/cancel', [PaymentController::class, 'cancelSubscription'])->name('subscription.cancel');
    Route::post('/subscription/change', [PaymentController::class, 'changePlan'])->name('subscription.change');

    // Payment history
    Route::get('/history/payment', [PaymentController::class, 'paymentHistory'])->name('payment.history');
    Route::get('/history/filter', [PaymentController::class, 'filterHistory'])->name('payment.history.filter');
    Route::get('/subscription/status', [PaymentController::class, 'subscriptionStatus'])->name('subscription.status');
});

// Social authentication routes
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');

// Protected social auth routes
Route::middleware(['auth'])->group(function () {
    Route::post('/auth/{provider}/unlink', [SocialAuthController::class, 'unlinkAccount'])->name('social.unlink');
    Route::post('/auth/{provider}/link', [SocialAuthController::class, 'linkAccount'])->name('social.link');
});

// Test email queue
Route::get('/test-queue-email', function () {
    $user = App\Models\User::first();

    if (!$user) {
        return "No user found!";
    }

    Mail::to($user->email)->queue(new WelcomeStudentMail($user));

    return "Email queued! Check your queue worker.";
});// routes/web.php (if you need web routes too)

//cert view public
Route::get('/certificates/verify/{hash}', [CertificateController::class, 'verify'])->name('certificates.verify');


Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::post('/push/subscriptions', [PushSubscriptionController::class, 'store']);
    Route::delete('/push/subscriptions', [PushSubscriptionController::class, 'destroy']);
    Route::post('/push/test', [PushSubscriptionController::class, 'test']);
});


Route::get('/test-push', function () {


    $user = auth()->user();

    if (!$user) {
        return 'Please login first';
    }

    if (!$user->hasPushSubscription()) {
        return 'User has no push subscriptions';
    }

    // Send test notification
    $service = new \App\Services\WebPush\WebPushService();
    $notification = $service->createNotification(
        'Test Notification',
        'This is a test notification from your custom web push implementation!',
        url('/'),
        '/icons/icon-192x192.png'
    );

    $result = $service->sendToUser($user, $notification);

    return response()->json([
        'message' => 'Test notification sent',
        'results' => $result,
    ]);
});


// Another routes
require __DIR__.'/admin.php';
require __DIR__.'/student.php';
require __DIR__.'/api.php';
