// Organization routes
Route::middleware(['auth', 'subscription:student_management'])->get('/org/students', [OrgStudentController::class, 'index']);
Route::middleware(['auth', 'subscription:ai_grading'])->post('/org/grading', [OrgGradingController::class, 'grade'])
