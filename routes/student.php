<?php
// routes/student.php

use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\CourseController;
use App\Http\Controllers\Student\QuizController;
use App\Http\Controllers\Student\ChatController;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\Student\CapstoneProjectController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'role:student'])->prefix('student')->name('student.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/progress-chart', [DashboardController::class, 'getProgressChart'])->name('dashboard.progress-chart');

    // Courses
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::middleware(['subscription:unlimited_ai_learning'])->get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
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
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/create', [ChatController::class, 'create'])->name('chat.create');
    Route::middleware([ 'subscription:ai_request'])->post('/chat', [ChatController::class, 'store'])->name('chat.store');
    //Route::get('/chat/{course}', [ChatController::class, 'showFromCourse'])->name('chat.showFromCourse');
    Route::get('/chat/{chatSession}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{chatSession}/send-message', [ChatController::class, 'sendMessage'])->name('chat.send-message');
    Route::post('/chat/{chatSession}/update-context', [ChatController::class, 'updateContext'])->name('chat.update-context');
    Route::delete('/chat/{chatSession}/close', [ChatController::class, 'closeSession'])->name('chat.close');
    Route::get('/chat/active-session', [ChatController::class, 'getActiveSession'])->name('chat.active-session');
    Route::get('/chat/{chatSession}/messages', [ChatController::class, 'getMessages'])->name('chat.messages');

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


});
