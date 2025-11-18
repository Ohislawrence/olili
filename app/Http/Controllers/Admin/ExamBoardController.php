<?php
// app/Http/Controllers/Admin/ExamBoardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamBoard;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExamBoardController extends Controller
{
    public function index()
    {
        $examBoards = ExamBoard::withCount(['courses', 'studentProfiles'])
            ->get();

        return Inertia::render('Admin/ExamBoards/Index', [
            'exam_boards' => $examBoards,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/ExamBoards/Create', [
            'countries' => $this->getCountriesList(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:exam_boards,name',
            'code' => 'required|string|max:255|unique:exam_boards,code',
            'description' => 'nullable|string',
            'country' => 'required|string|max:255',
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'string|max:255',
            'is_active' => 'boolean',
        ]);

        ExamBoard::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'country' => $request->country,
            'subjects' => $request->subjects,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('admin.exam-boards.index')
            ->with('success', 'Exam board created successfully.');
    }

    public function show(ExamBoard $examBoard)
    {
        if (!$examBoard->subjects) {
            $examBoard->subjects = [];
        }

        $examBoard->load(['courses.studentProfile.user', 'studentProfiles.user']);

        $recentCourses = $examBoard->courses()
            ->with(['studentProfile.user'])
            ->latest()
            ->limit(10)
            ->get();

        $popularSubjects = Course::where('exam_board_id', $examBoard->id)
            ->selectRaw('subject, COUNT(*) as course_count')
            ->groupBy('subject')
            ->orderBy('course_count', 'desc')
            ->limit(10)
            ->get();

        return Inertia::render('Admin/ExamBoards/Show', [
            'exam_board' => $examBoard,
            'recent_courses' => $recentCourses,
            'popular_subjects' => $popularSubjects,
        ]);
    }

    public function edit(ExamBoard $examBoard)
    {
        return Inertia::render('Admin/ExamBoards/Edit', [
            'exam_board' => $examBoard,
            'countries' => $this->getCountriesList(),
        ]);
    }

    public function update(Request $request, ExamBoard $examBoard)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:exam_boards,name,' . $examBoard->id,
            'code' => 'required|string|max:255|unique:exam_boards,code,' . $examBoard->id,
            'description' => 'nullable|string',
            'country' => 'required|string|max:255',
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'string|max:255',
            'is_active' => 'boolean',
        ]);

        $examBoard->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'country' => $request->country,
            'subjects' => $request->subjects,
            'is_active' => $request->is_active ?? $examBoard->is_active,
        ]);

        return redirect()->route('admin.exam-boards.index')
            ->with('success', 'Exam board updated successfully.');
    }

    public function destroy(ExamBoard $examBoard)
    {
        if ($examBoard->courses()->exists() || $examBoard->studentProfiles()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete exam board that is in use.');
        }

        $examBoard->delete();

        return redirect()->route('admin.exam-boards.index')
            ->with('success', 'Exam board deleted successfully.');
    }

    public function toggleActive(ExamBoard $examBoard)
    {
        $examBoard->update([
            'is_active' => !$examBoard->is_active,
        ]);

        $status = $examBoard->is_active ? 'activated' : 'deactivated';

        return redirect()->back()->with('success', "Exam board {$status} successfully.");
    }

    private function getCountriesList()
    {
        return [
            'Nigeria' => 'Nigeria',
            'Ghana' => 'Ghana',
            'Kenya' => 'Kenya',
            'South Africa' => 'South Africa',
            'United Kingdom' => 'United Kingdom',
            'United States' => 'United States',
            'Canada' => 'Canada',
            'International' => 'International',
            'Other' => 'Other',
        ];
    }
}
