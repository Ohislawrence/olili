<?php
// app/Http/Controllers/Admin/CourseSearchController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseSearchController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|min:2',
            'exclude_specialization' => 'nullable|exists:specializations,id',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $query = Course::where('created_by', 'admin')
            ->where('status', 'active');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('subject', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        if ($request->exclude_specialization) {
            $query->whereDoesntHave('specializations', function ($q) use ($request) {
                $q->where('specialization_id', $request->exclude_specialization);
            });
        }

        $courses = $query->limit($request->limit ?? 20)
            ->with('examBoard')
            ->get(['id', 'title', 'subject', 'level', 'estimated_duration_hours', 'exam_board_id']);

        return response()->json(['courses' => $courses]);
    }
}
