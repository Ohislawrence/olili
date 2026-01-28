<?php
// app/Http/Controllers/Admin/SpecializationController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Specialization;
use App\Models\SpecializationResource;
use App\Models\SpecializationTag;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SpecializationController extends Controller
{
    public function index(Request $request)
    {
        $query = Specialization::with(['creator', 'courses'])
            ->withCount(['courses', 'enrollments', 'activeEnrollments', 'completedEnrollments'])
            ->latest();

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%")
                  ->orWhere('target_exam', 'like', "%{$request->search}%")
                  ->orWhere('target_career', 'like', "%{$request->search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by exam
        if ($request->has('exam') && $request->exam) {
            $query->where('target_exam', $request->exam);
        }

        // Filter by career
        if ($request->has('career') && $request->career) {
            $query->where('target_career', $request->career);
        }

        $specializations = $query->paginate(20);

        // Get unique exams and careers for filters
        $exams = Specialization::distinct()->whereNotNull('target_exam')->pluck('target_exam');
        $careers = Specialization::distinct()->whereNotNull('target_career')->pluck('target_career');

        // Stats
        $stats = [
            'total' => Specialization::count(),
            'active' => Specialization::where('status', 'active')->where('is_public', true)->count(),
            'draft' => Specialization::where('status', 'draft')->count(),
            'featured' => Specialization::where('is_featured', true)->count(),
            'total_enrollments' => Specialization::sum('enrollment_count'),
            'total_completions' => Specialization::sum('completion_count'),
        ];

        return Inertia::render('Admin/Specializations/Index', [
            'specializations' => $specializations,
            'filters' => $request->only(['search', 'status', 'exam', 'career']),
            'exams' => $exams,
            'careers' => $careers,
            'stats' => $stats,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Specializations/Create', [
            'exam_options' => $this->getExamOptions(),
            'career_options' => $this->getCareerOptions(),
            'level_options' => $this->getLevelOptions(),
            'status_options' => $this->getStatusOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'description' => 'required|string',
            'target_exam' => 'nullable|string|max:100',
            'target_career' => 'nullable|string|max:100',
            'level' => 'required|string|in:primary,secondary,tertiary,professional',
            'price' => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'has_discount' => 'boolean',
            'learning_objectives' => 'nullable|array',
            'learning_objectives.*' => 'string|max:500',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'string|max:500',
            'target_skills' => 'nullable|array',
            'target_skills.*' => 'string|max:200',
            'career_opportunities' => 'nullable|array',
            'career_opportunities.*' => 'string|max:200',
            'min_electives' => 'nullable|integer|min:0',
            'max_electives' => 'nullable|integer|min:1|gt:min_electives',
            'is_featured' => 'boolean',
            'is_public' => 'boolean',
            'status' => 'required|in:draft,active,archived',
            'thumbnail' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:5120',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);

        DB::beginTransaction();

        try {
            // Handle thumbnail upload
            $thumbnailUrl = null;
            if ($request->hasFile('thumbnail')) {
                $path = $request->file('thumbnail')->store('specializations/thumbnails', 'public');
                $thumbnailUrl = Storage::url($path);
            }

            // Handle banner upload
            $bannerUrl = null;
            if ($request->hasFile('banner')) {
                $path = $request->file('banner')->store('specializations/banners', 'public');
                $bannerUrl = Storage::url($path);
            }

            // Create specialization
            $specialization = Specialization::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'short_description' => $validated['short_description'],
                'description' => $validated['description'],
                'target_exam' => $validated['target_exam'] ?? null,
                'target_career' => $validated['target_career'] ?? null,
                'level' => $validated['level'],
                'price' => $validated['price'] ?? 0,
                'discount_price' => $validated['discount_price'] ?? null,
                'has_discount' => $validated['has_discount'] ?? false,
                'learning_objectives' => $validated['learning_objectives'] ?? [],
                'prerequisites' => $validated['prerequisites'] ?? [],
                'target_skills' => $validated['target_skills'] ?? [],
                'career_opportunities' => $validated['career_opportunities'] ?? [],
                'min_electives' => $validated['min_electives'] ?? 0,
                'max_electives' => $validated['max_electives'] ?? null,
                'is_featured' => $validated['is_featured'] ?? false,
                'is_public' => $validated['is_public'] ?? false,
                'status' => $validated['status'],
                'thumbnail_url' => $thumbnailUrl,
                'banner_url' => $bannerUrl,
                'created_by_user_id' => auth()->id(),
            ]);

            // Add tags
            if (!empty($validated['tags'])) {
                foreach ($validated['tags'] as $tag) {
                    SpecializationTag::create([
                        'specialization_id' => $specialization->id,
                        'tag' => trim($tag),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.specializations.show', $specialization->id)
                ->with('success', 'Specialization created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create specialization: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to create specialization: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Specialization $specialization)
    {
        $specialization->load([
            'creator',
            'courses' => function ($query) {
                $query->with('examBoard')->orderBy('order');
            },
            'courses.creator',
            'courses.modules',
            'resources',
            'tags',
            'enrollments.user',
        ]);

        $availableCourses = Course::whereNotIn('id', $specialization->courses->pluck('id'))
            ->where('created_by', 'admin') // Only admin courses
            ->where('status', 'active')
            ->with('examBoard')
            ->get();

        $enrollmentStats = [
            'total' => $specialization->enrollments()->count(),
            'active' => $specialization->activeEnrollments()->count(),
            'completed' => $specialization->completedEnrollments()->count(),
            'average_progress' => round($specialization->enrollments()->avg('progress_percentage') ?? 0, 1),
            'completion_rate' => $specialization->enrollment_count > 0
                ? round(($specialization->completion_count / $specialization->enrollment_count) * 100, 1)
                : 0,
        ];

        return Inertia::render('Admin/Specializations/Show', [
            'specialization' => $specialization,
            'available_courses' => $availableCourses,
            'enrollment_stats' => $enrollmentStats,
            'exam_options' => $this->getExamOptions(),
            'career_options' => $this->getCareerOptions(),
            'level_options' => $this->getLevelOptions(),
            'status_options' => $this->getStatusOptions(),
            'resource_types' => $this->getResourceTypes(),
        ]);
    }

    public function edit(Specialization $specialization)
    {
        $specialization->load(['tags']);

        return Inertia::render('Admin/Specializations/Edit', [
            'specialization' => $specialization,
            'exam_options' => $this->getExamOptions(),
            'career_options' => $this->getCareerOptions(),
            'level_options' => $this->getLevelOptions(),
            'status_options' => $this->getStatusOptions(),
        ]);
    }

    public function update(Request $request, Specialization $specialization)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'description' => 'required|string',
            'target_exam' => 'nullable|string|max:100',
            'target_career' => 'nullable|string|max:100',
            'level' => 'required|string|in:primary,secondary,tertiary,professional',
            'price' => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'has_discount' => 'boolean',
            'learning_objectives' => 'nullable|array',
            'learning_objectives.*' => 'string|max:500',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'string|max:500',
            'target_skills' => 'nullable|array',
            'target_skills.*' => 'string|max:200',
            'career_opportunities' => 'nullable|array',
            'career_opportunities.*' => 'string|max:200',
            'min_electives' => 'nullable|integer|min:0',
            'max_electives' => 'nullable|integer|min:1|gt:min_electives',
            'is_featured' => 'boolean',
            'is_public' => 'boolean',
            'status' => 'required|in:draft,active,archived',
            'thumbnail' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:5120',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);

        DB::beginTransaction();

        try {
            // Handle thumbnail update
            if ($request->hasFile('thumbnail')) {
                // Delete old thumbnail if exists
                if ($specialization->thumbnail_url) {
                    $oldPath = str_replace('/storage/', '', parse_url($specialization->thumbnail_url, PHP_URL_PATH));
                    Storage::disk('public')->delete($oldPath);
                }

                $path = $request->file('thumbnail')->store('specializations/thumbnails', 'public');
                $validated['thumbnail_url'] = Storage::url($path);
            }

            // Handle banner update
            if ($request->hasFile('banner')) {
                // Delete old banner if exists
                if ($specialization->banner_url) {
                    $oldPath = str_replace('/storage/', '', parse_url($specialization->banner_url, PHP_URL_PATH));
                    Storage::disk('public')->delete($oldPath);
                }

                $path = $request->file('banner')->store('specializations/banners', 'public');
                $validated['banner_url'] = Storage::url($path);
            }

            // Update slug if name changed
            if ($specialization->name !== $validated['name']) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            unset($validated['thumbnail'], $validated['banner']);

            // Update specialization
            $specialization->update($validated);

            // Update tags
            if (isset($validated['tags'])) {
                // Delete existing tags
                $specialization->tags()->delete();

                // Add new tags
                foreach ($validated['tags'] as $tag) {
                    SpecializationTag::create([
                        'specialization_id' => $specialization->id,
                        'tag' => trim($tag),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.specializations.show', $specialization->id)
                ->with('success', 'Specialization updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update specialization: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to update specialization: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Specialization $specialization)
    {
        // Check if there are active enrollments
        if ($specialization->activeEnrollments()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete specialization with active enrollments. Please archive it instead.');
        }

        try {
            // Delete thumbnails and banners
            if ($specialization->thumbnail_url) {
                $path = str_replace('/storage/', '', parse_url($specialization->thumbnail_url, PHP_URL_PATH));
                Storage::disk('public')->delete($path);
            }

            if ($specialization->banner_url) {
                $path = str_replace('/storage/', '', parse_url($specialization->banner_url, PHP_URL_PATH));
                Storage::disk('public')->delete($path);
            }

            // Delete resources files
            foreach ($specialization->resources as $resource) {
                if ($resource->file_url) {
                    $path = str_replace('/storage/', '', parse_url($resource->file_url, PHP_URL_PATH));
                    Storage::disk('public')->delete($path);
                }
            }

            $specialization->delete();

            return redirect()->route('admin.specializations.index')
                ->with('success', 'Specialization deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to delete specialization: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete specialization: ' . $e->getMessage());
        }
    }

    public function publish(Specialization $specialization)
    {
        $specialization->publish();

        return redirect()->back()->with('success', 'Specialization published successfully!');
    }

    public function unpublish(Specialization $specialization)
    {
        $specialization->unpublish();

        return redirect()->back()->with('success', 'Specialization unpublished successfully!');
    }

    public function toggleFeatured(Specialization $specialization)
    {
        $specialization->update([
            'is_featured' => !$specialization->is_featured,
        ]);

        $action = $specialization->is_featured ? 'featured' : 'unfeatured';
        return redirect()->back()->with('success', "Specialization {$action} successfully!");
    }

    // Course Management Methods
    public function addCourse(Request $request, Specialization $specialization)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'is_required' => 'boolean',
            'category' => 'nullable|in:core,elective,supplementary',
            'notes' => 'nullable|string',
        ]);

        $course = Course::findOrFail($request->course_id);

        $specialization->addCourse($course,
            $request->is_required ?? true,
            [
                'category' => $request->category ?? ($request->is_required ? 'core' : 'elective'),
                'notes' => $request->notes,
            ]
        );

        return redirect()->back()->with('success', 'Course added to specialization!');
    }

    public function removeCourse(Specialization $specialization, Course $course)
    {
        $specialization->removeCourse($course);

        return redirect()->back()->with('success', 'Course removed from specialization!');
    }

    public function updateCourseOrder(Request $request, Specialization $specialization)
    {
        $request->validate([
            'courses' => 'required|array',
            'courses.*' => 'exists:courses,id',
        ]);

        $specialization->reorderCourses($request->courses);

        return response()->json(['success' => true, 'message' => 'Course order updated!']);
    }

    public function updateCourseSettings(Request $request, Specialization $specialization, Course $course)
    {
        $request->validate([
            'is_required' => 'boolean',
            'category' => 'nullable|in:core,elective,supplementary',
            'recommended_weeks' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $specialization->courses()->updateExistingPivot($course->id, [
            'is_required' => $request->is_required ?? true,
            'category' => $request->category ?? 'core',
            'recommended_weeks' => $request->recommended_weeks,
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Course settings updated!');
    }

    // Resource Management Methods
    public function addResource(Request $request, Specialization $specialization)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:resource,past_question,guide,video,link',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // 10MB max
            'external_url' => 'nullable|url',
            'is_free' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $fileUrl = null;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('specializations/resources', 'public');
            $fileUrl = Storage::url($path);
        }

        SpecializationResource::create([
            'specialization_id' => $specialization->id,
            'title' => $request->title,
            'type' => $request->type,
            'description' => $request->description,
            'file_url' => $fileUrl,
            'external_url' => $request->external_url,
            'is_free' => $request->is_free ?? true,
            'is_active' => $request->is_active ?? true,
            'order' => $specialization->resources()->max('order') + 1,
        ]);

        return redirect()->back()->with('success', 'Resource added successfully!');
    }

    public function updateResource(Request $request, Specialization $specialization, SpecializationResource $resource)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:resource,past_question,guide,video,link',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
            'external_url' => 'nullable|url',
            'is_free' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $fileUrl = $resource->file_url;
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($resource->file_url) {
                $oldPath = str_replace('/storage/', '', parse_url($resource->file_url, PHP_URL_PATH));
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('file')->store('specializations/resources', 'public');
            $fileUrl = Storage::url($path);
        }

        $resource->update([
            'title' => $request->title,
            'type' => $request->type,
            'description' => $request->description,
            'file_url' => $fileUrl,
            'external_url' => $request->external_url,
            'is_free' => $request->is_free ?? true,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->back()->with('success', 'Resource updated successfully!');
    }

    public function deleteResource(Specialization $specialization, SpecializationResource $resource)
    {
        // Delete file if exists
        if ($resource->file_url) {
            $path = str_replace('/storage/', '', parse_url($resource->file_url, PHP_URL_PATH));
            Storage::disk('public')->delete($path);
        }

        $resource->delete();

        return redirect()->back()->with('success', 'Resource deleted successfully!');
    }

    public function reorderResources(Request $request, Specialization $specialization)
    {
        $request->validate([
            'resources' => 'required|array',
            'resources.*' => 'exists:specialization_resources,id',
        ]);

        foreach ($request->resources as $order => $resourceId) {
            SpecializationResource::where('id', $resourceId)
                ->update(['order' => $order + 1]);
        }

        return response()->json(['success' => true, 'message' => 'Resource order updated!']);
    }

    // Enrollment Management
    public function enrollments(Specialization $specialization)
    {
        $enrollments = $specialization->enrollments()
            ->with(['user', 'user.studentProfile'])
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Specializations/Enrollments', [
            'specialization' => $specialization,
            'enrollments' => $enrollments,
        ]);
    }

    public function bulkEnroll(Request $request, Specialization $specialization)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $enrolledCount = 0;
        $failedUsers = [];

        foreach ($request->user_ids as $userId) {
            $user = \App\Models\User::find($userId);

            if ($user && $specialization->enrollStudent($user)) {
                $enrolledCount++;
            } else {
                $failedUsers[] = $userId;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Enrolled {$enrolledCount} users successfully.",
            'failed' => $failedUsers,
        ]);
    }

    // Helper Methods
    private function getExamOptions()
    {
        return [
            'JAMB' => 'JAMB (UTME)',
            'WAEC' => 'WAEC (SSCE)',
            'NECO' => 'NECO (SSCE)',
            'SAT' => 'SAT',
            'IELTS' => 'IELTS',
            'TOEFL' => 'TOEFL',
            'GRE' => 'GRE',
            'GMAT' => 'GMAT',
            'Custom' => 'Custom Exam',
        ];
    }

    private function getCareerOptions()
    {
        return [
            'Medicine' => 'Medicine',
            'Engineering' => 'Engineering',
            'Law' => 'Law',
            'Business' => 'Business',
            'Computer Science' => 'Computer Science',
            'Arts & Humanities' => 'Arts & Humanities',
            'Social Sciences' => 'Social Sciences',
            'Natural Sciences' => 'Natural Sciences',
            'Education' => 'Education',
            'Agriculture' => 'Agriculture',
            'Architecture' => 'Architecture',
            'Pharmacy' => 'Pharmacy',
            'Nursing' => 'Nursing',
            'Veterinary Medicine' => 'Veterinary Medicine',
            'Dentistry' => 'Dentistry',
        ];
    }

    private function getLevelOptions()
    {
        return [
            'primary' => 'Primary School',
            'secondary' => 'Secondary School',
            'tertiary' => 'Tertiary/University',
            'professional' => 'Professional/Continuing Education',
        ];
    }

    private function getStatusOptions()
    {
        return [
            'draft' => 'Draft',
            'active' => 'Active',
            'archived' => 'Archived',
        ];
    }

    private function getResourceTypes()
    {
        return [
            'resource' => 'Study Resource',
            'past_question' => 'Past Questions',
            'guide' => 'Study Guide',
            'video' => 'Video Lesson',
            'link' => 'External Link',
        ];
    }
}
