<?php
// routes/student.php

use App\Http\Controllers\Student\NotificationController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\CourseController;
use App\Http\Controllers\Student\QuizController;
use App\Http\Controllers\Student\ChatController;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\Student\CapstoneProjectController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Student\CatalogController;
use App\Http\Controllers\Student\CourseShareController;
use App\Http\Controllers\Student\CourseTutorController;
use App\Http\Controllers\Student\FlashcardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'role:student'])->prefix('student')->name('student.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/progress-chart', [DashboardController::class, 'getProgressChart'])->name('dashboard.progress-chart');

    // Courses
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::middleware(['subscription:create_course'])->get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::middleware(['subscription:create_course'])->post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::post('/courses/{course}/start', [CourseController::class, 'startCourse'])->name('courses.start');
    Route::get('/courses/{course}/learn', [CourseController::class, 'learn'])->name('courses.learn');
    Route::put('/courses/{course}/pause', [CourseController::class, 'pauseCourse'])->name('courses.pause');
    Route::put('/courses/{course}/resume', [CourseController::class, 'resumeCourse'])->name('courses.resume');
    Route::post('/outlines/{outline}/complete', [CourseController::class, 'completeOutline'])->name('outlines.complete');
    Route::post('/outlines/{outline}/generate-content', [CourseController::class, 'generateContent'])->name('outlines.generate-content');
    Route::post('/courses/{course}/update-progress', [CourseController::class, 'updateProgress'])->name('courses.update-progress');
    Route::post('/courses/complete/topic/{topic}', [CourseController::class, 'completeTopic'])->name('courses.complete-topic');
    Route::get('/courses/{course}/chat/initialize', [ChatController::class, 'initializePopupChat'])->name('courses.chat.initialize');

    // Content Quiz - Fixed route names to avoid conflicts
    Route::post('/courses/{course}/outlines/{outline}/generate-quiz', [CourseController::class, 'generateQuiz'])->name('outlines.generate-quiz');
    Route::post('/quizzes/{quiz}/start', [QuizController::class, 'startAttempt'])->name('quizzes.start');
    Route::post('/quiz-attempts/{attempt}/submit', [QuizController::class, 'submitAttempt'])->name('quizzes.submit');
    Route::post('/courses/quiz-attempts/{attempt}/result', [QuizController::class, 'getCourseQuizResults'])->name('quiz-attempts.result');


    // Course Catalog
    Route::prefix('catalog')->name('catalog.')->group(function () {
        Route::get('/', [CatalogController::class, 'index'])->name('index');
        Route::get('/my-courses', [CatalogController::class, 'myEnrolledCourses'])->name('my-courses');
        Route::get('/courses/{course}', [CatalogController::class, 'show'])->name('show');
        Route::get('/courses/{course}/preview', [CatalogController::class, 'preview'])->name('preview');
        Route::post('/courses/{course}/enroll', [CatalogController::class, 'enroll'])->name('enroll');
        Route::delete('/courses/{course}/unenroll', [CatalogController::class, 'unenroll'])->name('unenroll');
    });

    //share courses
    Route::post('courses/{course}/share', [CourseShareController::class, 'share'])->name('course.share');
    Route::get('courses/share/{token}/accept', [CourseShareController::class, 'accept'])->name('course.share.accept');
    Route::get('courses/share/{token}/reject', [CourseShareController::class, 'reject'])->name('share.reject');
    Route::get('courses/shared/pending', [CourseShareController::class, 'pendingShares'])->name('courses.shared.pending');
    Route::get('courses/me/shared', [CourseShareController::class, 'sharedCourses'])->name('courses.shared');

    // General Quizzes - Fixed parameter order and names
    Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes.index');
    //Route::get('/quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
    //Route::get('/quizzes/{quiz}/start', [QuizController::class, 'startAttempt'])->name('c.quizzes.start');
    //Route::get('/quiz-attempts/{attempt}/continue', [QuizController::class, 'continueAttempt'])->name('quiz-attempts.continue');
    //Route::post('/quiz-attempts/{attempt}/submit', [QuizController::class, 'submitAttempt'])->name('quiz-attempts.submit');
    //Route::get('/quiz-attempts/{attempt}/result', [QuizController::class, 'showResult'])->name('quiz-attempts.result');
    //Route::get('/quizzes/{quiz}/history', [QuizController::class, 'getQuizHistory'])->name('quizzes.history');

    // Flashcard Routes - Fixed route parameter names
    Route::get('/flashcards', [FlashcardController::class, 'index'])->name('flashcards.index');
    Route::get('/flashcards/create', [FlashcardController::class, 'create'])->name('flashcards.create');
    Route::post('/flashcards', [FlashcardController::class, 'store'])->name('flashcards.store');
    Route::get('/flashcards/{flashcardSet}', [FlashcardController::class, 'show'])->name('flashcards.show');
    Route::get('/flashcards/{flashcardSet}/study', [FlashcardController::class, 'study'])->name('flashcards.study');
    Route::post('/flashcard-items/{flashcard}/progress', [FlashcardController::class, 'updateProgress'])->name('flashcards.update-progress');
    Route::delete('/flashcards/{flashcardSet}', [FlashcardController::class, 'destroy'])->name('flashcards.destroy');
    Route::get('/courses/{course}/outlines', [FlashcardController::class, 'getCourseOutlines'])->name('courses.outlines');
    Route::post('/flashcards/{flashcardSet}/reset-progress', [FlashcardController::class, 'resetProgress'])->name('flashcards.reset-progress');

    // Notification Routes - Added middleware for consistency
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications', [NotificationController::class, 'clearAll'])->name('notifications.clear-all');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');


    // Profile - Added missing middleware
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('update-password');
        Route::put('/learning-goals', [ProfileController::class, 'updateLearningGoals'])->name('update-learning-goals');
        Route::get('/progress-stats', [ProfileController::class, 'getProgressStats'])->name('progress-stats');
        Route::get('/course-progress', [ProfileController::class, 'getCourseProgress'])->name('course-progress');
    });

    // Capstone Projects - Fixed route parameters and added missing routes
    Route::middleware(['subscription:capstone_projects'])->group(function () {
        Route::get('/capstone-projects', [CapstoneProjectController::class, 'index'])->name('capstone-projects.index');
        Route::get('/capstone-projects/create/{course}', [CapstoneProjectController::class, 'create'])->name('capstone-projects.create');
        Route::post('/capstone-projects', [CapstoneProjectController::class, 'store'])->name('capstone-projects.store');
        Route::get('/capstone-projects/{capstoneProject}', [CapstoneProjectController::class, 'show'])->name('capstone-projects.show');
        Route::post('/capstone-projects/{capstoneProject}/submit', [CapstoneProjectController::class, 'submit'])->name('capstone-projects.submit');
        Route::get('/capstone-projects/{capstoneProject}/download/{fileIndex}', [CapstoneProjectController::class, 'downloadFile'])->name('capstone-projects.download-file');
        Route::delete('/capstone-projects/{capstoneProject}', [CapstoneProjectController::class, 'destroy'])->name('capstone-projects.destroy');
    });

    // Course Tutor Chat Routes - Fixed nested routing
    Route::prefix('courses/{course}')->name('courses.')->group(function () {
        Route::prefix('tutor')->name('tutor.')->group(function () {
            Route::get('/session', [CourseTutorController::class, 'getCourseSession'])->name('session');
            Route::get('/topics', [CourseTutorController::class, 'getCourseTopics'])->name('topics');
            Route::post('/message', [CourseTutorController::class, 'sendMessage'])->name('send-message');
            Route::post('/context', [CourseTutorController::class, 'updateContext'])->name('update-context');
            Route::get('/sessions/{chatSession}/messages', [CourseTutorController::class, 'getMessages'])->name('messages');
            Route::delete('/sessions/{chatSession}/close', [CourseTutorController::class, 'closeSession'])->name('close-session');
        });
    });
});
