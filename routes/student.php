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
    Route::post('/courses/complete/{outline}/topic', [CourseController::class, 'completeTopic'])->name('courses.completeTopic');
    Route::get('/courses/{course}/chat/initialize', [ChatController::class, 'initializePopupChat'])->name('courses.chat.initialize');

    //contentQuiz
    Route::post('/courses/{course}/outlines/{outline}/generate-quiz', [CourseController::class, 'generateQuiz'])
    ->name('outlines.generate-quiz');
    Route::post('/quizzes/{quiz}/start', [QuizController::class, 'start'])->name('quizzes.start');
    Route::post('/quiz-submit/{attempt}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');
    Route::post('/quiz-result/{attempt}/submit', [QuizController::class, 'getDetailedResults'])->name('quizzes.result');

    // General Quizzes
    Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes.index');
    Route::get('/quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::get('/quizzes/{quiz}/start', [QuizController::class, 'startAttempt'])->name('quizzes.start.attempt');
    Route::get('/quizzes/{attempt}/continue/{quiz}', [QuizController::class, 'continueAttempt'])->name('quizzes.continue.attempt');
    Route::post('/quiz-attempts/{attempt}/submit', [QuizController::class, 'submitAttempt'])->name('quiz-attempts.submit');
    Route::get('/quiz-attempts/{attempt}/result', [QuizController::class, 'showResult'])->name('quiz-attempts.result');
    Route::get('/quizzes/{quiz}/history', [QuizController::class, 'getQuizHistory'])->name('quizzes.history');

    // Chat
   // Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    //Route::get('/chat/create', [ChatController::class, 'create'])->name('chat.create');
    //Route::middleware([ 'subscription:basic_chat'])->post('/chat/', [ChatController::class, 'store'])->name('chat.store');
    //Route::get('/chat/{course}', [ChatController::class, 'showFromCourse'])->name('chat.showFromCourse');
   // Route::get('/chat/{chatSession}', [ChatController::class, 'show'])->name('chat.show');
    //Route::post('/courses/{course}/chat/{chatSession}/send-message', [ChatController::class, 'sendMessage'])->name('courses.chat.send-message');
    //Route::post('/chat/{chatSession}/update-context', [ChatController::class, 'updateContext'])->name('chat.update-context');
   // Route::delete('/chat/{chatSession}/close', [ChatController::class, 'closeSession'])->name('chat.close');
    //Route::get('/chat/active-session', [ChatController::class, 'getActiveSession'])->name('chat.active-session');
    //Route::get('/chat/{chatSession}/messages', [ChatController::class, 'getMessages'])->name('chat.messages');

    // Flashcard Routes
    Route::get('/flashcards', [FlashcardController::class, 'index'])->name('flashcards.index');
    Route::get('/flashcards/create', [FlashcardController::class, 'create'])->name('flashcards.create');
    Route::post('/flashcards', [FlashcardController::class, 'store'])->name('flashcards.store');
    Route::get('/flashcards/{flashcardSet}', [FlashcardController::class, 'show'])->name('flashcards.show');
    Route::get('/flashcards/{flashcardSet}/study', [FlashcardController::class, 'study'])->name('flashcards.study');
    Route::post('/flashcards/{flashcard}/progress', [FlashcardController::class, 'updateProgress'])->name('flashcards.update-progress');
    Route::delete('/flashcards/{flashcardSet}', [FlashcardController::class, 'destroy'])->name('flashcards.destroy');
    Route::get('/courses/{course}/outlines', [FlashcardController::class, 'getCourseOutlines'])->name('courses.outlines');
    Route::post('/flashcards/{flashcardSet}/reset-progress', [FlashcardController::class, 'resetProgress'])->name('flashcards.reset-progress');

     // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications', [NotificationController::class, 'clearAll'])->name('notifications.clear-all');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::put('/profile/learning-goals', [ProfileController::class, 'updateLearningGoals'])->name('profile.update-learning-goals');
    Route::get('/profile/progress-stats', [ProfileController::class, 'getProgressStats'])->name('profile.progress-stats');
    Route::get('/profile/course-progress', [ProfileController::class, 'getCourseProgress'])->name('profile.course-progress');

    // Capstone Projects
    Route::get('/capstone-projects', [CapstoneProjectController::class, 'index'])->name('capstone-projects.index');
    Route::get('/capstone-projects/create/{course}', [CapstoneProjectController::class, 'create'])->name('capstone-projects.create');
    Route::get('/capstone-projects/{capstoneProject}', [CapstoneProjectController::class, 'show'])->name('capstone-projects.show');
    Route::post('/capstone-projects/{capstoneProject}/submit', [CapstoneProjectController::class, 'submit'])->name('capstone-projects.submit');
    Route::get('/capstone-projects/{capstoneProject}/download/{fileIndex}', [CapstoneProjectController::class, 'downloadFile'])->name('capstone-projects.download-file');


    // Course Tutor Chat Routes
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
