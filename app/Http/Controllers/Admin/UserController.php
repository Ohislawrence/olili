<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\ExamBoard;
use App\Models\LoginHistory;
use App\Services\LoginTrackerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    protected $loginTrackerService;

    public function __construct(LoginTrackerService $loginTrackerService)
    {
        $this->loginTrackerService = $loginTrackerService;
    }

    public function index(Request $request)
    {
        $query = User::with(['roles', 'studentProfile.examBoard'])
            ->withCount(['courses', 'quizAttempts', 'loginHistories']);

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhereHas('studentProfile', function ($q) use ($request) {
                      $q->where('current_level', 'like', "%{$request->search}%")
                        ->orWhere('target_level', 'like', "%{$request->search}%");
                  });
            });
        }

        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->role($request->role);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'locked') {
                $query->whereNotNull('account_locked_until')
                      ->where('account_locked_until', '>', now());
            }
        }

        // Filter by verification status
        if ($request->has('verified') && $request->verified !== '') {
            if ($request->verified === '1') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        $users = $query->latest()->paginate(20);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role', 'status', 'verified']),
            'roles' => Role::all()->pluck('name'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Users/Create', [
            'roles' => Role::all()->pluck('name'),
            'exam_boards' => ExamBoard::active()->get(),
            'levels' => ['beginner', 'intermediate', 'advanced', 'expert'],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|string|in:admin,tutor,student',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'bio' => 'nullable|string|max:1000',
            'is_active' => 'boolean',

            // Student profile fields
            'exam_board_id' => 'nullable|exists:exam_boards,id',
            'current_level' => 'required_if:role,student|in:beginner,intermediate,advanced,expert',
            'target_level' => 'required_if:role,student|in:beginner,intermediate,advanced,expert',
            'target_completion_date' => 'required_if:role,student|date|after:today',
            'weekly_study_hours' => 'required_if:role,student|integer|min:1|max:40',
            'current_grade' => 'nullable|numeric|between:0,100',
            'target_grade' => 'nullable|numeric|between:0,100',
            'learning_goals' => 'nullable|array',
            'learning_goals.*' => 'string|max:500',
            'learning_preferences' => 'nullable|array',
        ]);

        \DB::transaction(function () use ($request) {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'bio' => $request->bio,
                'is_active' => $request->is_active ?? true,
                'email_verified_at' => $request->email_verified ? now() : null,
            ]);

            // Assign role
            $user->assignRole($request->role);

            // Create student profile if role is student
            if ($request->role === 'student') {
                StudentProfile::create([
                    'user_id' => $user->id,
                    'exam_board_id' => $request->exam_board_id,
                    'current_level' => $request->current_level,
                    'target_level' => $request->target_level,
                    'target_completion_date' => $request->target_completion_date,
                    'weekly_study_hours' => $request->weekly_study_hours,
                    'current_grade' => $request->current_grade,
                    'target_grade' => $request->target_grade,
                    'learning_goals' => $request->learning_goals,
                    'learning_preferences' => $request->learning_preferences,
                ]);
            }
        });

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load([
    'roles',
    'currentSubscription.plan',
    'activeSubscription.plan',
]);

// Load role-specific relationships
if ($user->hasRole('student')) {
    $user->load([
        'studentProfile.examBoard',
        'courses' => function ($query) {
            $query->withCount([
                'outlines',
                'outlines as completed_outlines_count' => function ($q) {
                    $q->where('is_completed', true);
                }
            ]);
        },
        'quizAttempts.quiz',
        'progressTracking',
    ]);
}

if ($user->hasRole('tutor')) {
    $user->load([
        'tutorProfile',
        'courses.students',
        'students.progressTracking',
    ]);
}

if ($user->hasRole('organization')) {
    $user->load([
        'organizationProfile',
        'tutors.tutorProfile',
        'students.studentProfile',
    ]);
}

// Load common relationships for all roles
$user->load([
    'aiUsageLogs' => function ($query) {
        $query->latest()->limit(10);
    },
    'chatSessions' => function ($query) {
        $query->latest()->limit(5);
    },
]);

        $stats = [
            'total_courses' => $user->courses->count(),
            'completed_courses' => $user->courses->where('status', 'completed')->count(),
            'total_quiz_attempts' => $user->quiz_attempts_count,
            'average_quiz_score' => $user->quizAttempts->avg('percentage'),
            'total_chat_sessions' => $user->chatSessions->count(),
            'total_logins' => $user->login_histories_count,
        ];

        $loginStats = $this->loginTrackerService->getUserLoginStats($user);

        return Inertia::render('Admin/Users/Show', [
            'user' => $user,
            'stats' => $stats,
            'loginStats' => $loginStats,
        ]);
    }

    public function edit(User $user)
    {
        $user->load(['roles', 'studentProfile']);

        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
            'roles' => Role::all()->pluck('name'),
            'exam_boards' => ExamBoard::active()->get(),
            'levels' => ['beginner', 'intermediate', 'advanced', 'expert'],
            'current_role' => $user->roles->first()->name ?? null,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:admin,tutor,student',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'bio' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'email_verified' => 'boolean',

            // Student profile fields
            'exam_board_id' => 'nullable|exists:exam_boards,id',
            'current_level' => 'required_if:role,student|in:beginner,intermediate,advanced,expert',
            'target_level' => 'required_if:role,student|in:beginner,intermediate,advanced,expert',
            'target_completion_date' => 'required_if:role,student|date|after:today',
            'weekly_study_hours' => 'required_if:role,student|integer|min:1|max:40',
            'current_grade' => 'nullable|numeric|between:0,100',
            'target_grade' => 'nullable|numeric|between:0,100',
            'learning_goals' => 'nullable|array',
            'learning_goals.*' => 'string|max:500',
            'learning_preferences' => 'nullable|array',
        ]);

        \DB::transaction(function () use ($request, $user) {
            // Update user
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'bio' => $request->bio,
                'is_active' => $request->is_active ?? $user->is_active,
                'email_verified_at' => $request->email_verified ? now() : null,
            ]);

            // Update role if changed
            $currentRole = $user->roles->first()->name ?? null;
            if ($currentRole !== $request->role) {
                $user->syncRoles([$request->role]);
            }

            // Update or create student profile
            if ($request->role === 'student') {
                $profileData = [
                    'exam_board_id' => $request->exam_board_id,
                    'current_level' => $request->current_level,
                    'target_level' => $request->target_level,
                    'target_completion_date' => $request->target_completion_date,
                    'weekly_study_hours' => $request->weekly_study_hours,
                    'current_grade' => $request->current_grade,
                    'target_grade' => $request->target_grade,
                    'learning_goals' => $request->learning_goals,
                    'learning_preferences' => $request->learning_preferences,
                ];

                if ($user->studentProfile) {
                    $user->studentProfile->update($profileData);
                } else {
                    $profileData['user_id'] = $user->id;
                    StudentProfile::create($profileData);
                }
            } elseif ($user->studentProfile) {
                // Remove student profile if role changed from student
                $user->studentProfile->delete();
            }
        });

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user)
    {
        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return redirect()->back()->with('success', "User {$status} successfully.");
    }

    public function unlockAccount(User $user)
    {
        $user->update([
            'failed_login_attempts' => 0,
            'account_locked_until' => null,
        ]);

        return redirect()->back()->with('success', 'User account unlocked successfully.');
    }

    public function impersonate(User $user)
    {
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Only administrators can impersonate users.');
        }

        auth()->user()->impersonate($user);

        return redirect()->route('student.dashboard')
            ->with('success', "Now impersonating {$user->name}.");
    }

    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Password updated successfully.');
    }

    public function loginHistory(User $user, Request $request)
    {
        $query = $user->loginHistories()->with('user')->latest();

        // Filter by status
        if ($request->has('status') && $request->status) {
            if ($request->status === 'success') {
                $query->where('was_successful', true);
            } elseif ($request->status === 'failed') {
                $query->where('was_successful', false);
            }
        }

        // Filter by device
        if ($request->has('device') && $request->device) {
            $query->where('device_type', $request->device);
        }

        $loginHistory = $query->paginate(20);

        $loginStats = $this->loginTrackerService->getUserLoginStats($user);

        return Inertia::render('Admin/Users/LoginHistory', [
            'user' => $user,
            'loginHistory' => $loginHistory,
            'loginStats' => $loginStats,
            'filters' => $request->only(['status', 'device']),
        ]);
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
        ]);

        $users = User::whereIn('id', $request->users)->get();

        switch ($request->action) {
            case 'activate':
                User::whereIn('id', $request->users)->update(['is_active' => true]);
                $message = 'Selected users activated successfully.';
                break;

            case 'deactivate':
                User::whereIn('id', $request->users)->update(['is_active' => false]);
                $message = 'Selected users deactivated successfully.';
                break;

            case 'delete':
                // Prevent self-deletion
                $usersToDelete = $users->filter(function ($user) {
                    return $user->id !== auth()->id();
                });

                User::whereIn('id', $usersToDelete->pluck('id'))->delete();
                $message = 'Selected users deleted successfully.';
                break;
        }

        return redirect()->back()->with('success', $message);
    }
}
