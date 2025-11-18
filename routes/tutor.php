// Tutor routes
Route::middleware(['auth', 'subscription:create_courses'])->post('/tutor/courses', [TutorCourseController::class, 'store']);
Route::middleware(['auth', 'subscription:ai_course_creation'])->post('/tutor/ai-content', [TutorAiController::class, 'generate']);
Route::middleware(['auth', 'subscription:advanced_analytics'])->get('/tutor/analytics', [TutorAnalyticsController::class, 'index'])
