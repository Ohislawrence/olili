<?php
// app/Http/Controllers/Student/CapstoneProjectController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CapstoneProject;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class CapstoneProjectController extends Controller
{
    public function index(Request $request)
    {
        $student = auth()->user();

        $query = CapstoneProject::with(['course'])
            ->whereHas('course', function ($q) use ($student) {
                $q->where('student_profile_id', $student->studentProfile->id);
            });

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $projects = $query->latest()
            ->paginate(10);

        $stats = [
            'total_projects' => $projects->total(),
            'submitted' => CapstoneProject::whereHas('course', function ($q) use ($student) {
                $q->where('student_profile_id', $student->studentProfile->id);
            })->where('status', 'submitted')->count(),
            'graded' => CapstoneProject::whereHas('course', function ($q) use ($student) {
                $q->where('student_profile_id', $student->studentProfile->id);
            })->where('status', 'graded')->count(),
        ];

        return Inertia::render('Student/CapstoneProjects/Index', [
            'projects' => $projects,
            'stats' => $stats,
            'filters' => $request->only(['status']),
        ]);
    }

    public function show(CapstoneProject $capstoneProject)
    {
        //$this->authorize('view', $capstoneProject);

        $capstoneProject->load(['course']);

        return Inertia::render('Student/CapstoneProjects/Show', [
            'project' => $capstoneProject,
        ]);
    }

    public function submit(CapstoneProject $capstoneProject, Request $request)
    {
        //$this->authorize('update', $capstoneProject);

        $request->validate([
            'submission' => 'required|string|min:100',
            'files' => 'nullable|array',
            'files.*' => 'file|max:10240', // 10MB max
        ]);

        $filePaths = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('capstone-submissions/' . $capstoneProject->id, 'public');
                $filePaths[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                ];
            }
        }

        $capstoneProject->submitProject($request->submission, $filePaths);

        return redirect()->route('student.capstone-projects.show', $capstoneProject->id)
            ->with('success', 'Project submitted successfully! It will be graded soon.');
    }

    public function create(Course $course)
    {
        //$this->authorize('view', $course);

        // Check if course is completed or near completion
        if ($course->progress_percentage < 80) {
            return redirect()->route('student.courses.show', $course->id)
                ->with('error', 'You need to complete at least 80% of the course before starting the capstone project.');
        }

        // Check if project already exists
        if ($course->capstoneProject) {
            return redirect()->route('student.capstone-projects.show', $course->capstoneProject->id);
        }

        return Inertia::render('Student/CapstoneProjects/Create', [
            'course' => $course,
        ]);
    }

    public function downloadFile(CapstoneProject $capstoneProject, $fileIndex)
    {
        //$this->authorize('view', $capstoneProject);

        $files = $capstoneProject->submission_files ?? [];

        if (!isset($files[$fileIndex])) {
            abort(404, 'File not found.');
        }

        $file = $files[$fileIndex];

        if (!Storage::disk('public')->exists($file['path'])) {
            abort(404, 'File not found on server.');
        }

        return Storage::disk('public')->download($file['path'], $file['name']);
    }
}
