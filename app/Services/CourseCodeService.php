<?php
// app/Services/CourseCodeService.php

namespace App\Services;

use App\Models\Course;
use App\Models\ExamBoard;

class CourseCodeService
{
    /**
     * Generate a course code with multiple format options
     */
    public function generate(Course $course, string $format = 'standard'): string
    {
        return match($format) {
            'compact' => $this->generateCompactCode($course),
            'detailed' => $this->generateDetailedCode($course),
            'standard' => $this->generateStandardCode($course),
            default => $this->generateStandardCode($course),
        };
    }

    /**
     * Standard format: SUBJ-EB-LEV-001
     */
    protected function generateStandardCode(Course $course): string
    {
        $subjectCode = $this->getSubjectCode($course->subject);
        $examBoardCode = $this->getExamBoardCode($course->exam_board_id);
        $levelCode = $this->getLevelCode($course->level);
        $sequence = $this->getSequenceNumber($subjectCode, $examBoardCode, $levelCode);

        $parts = array_filter([$subjectCode, $examBoardCode, $levelCode, $sequence]);

        return implode('-', $parts);
    }

    /**
     * Compact format: SUBEBL001 (no dashes)
     */
    protected function generateCompactCode(Course $course): string
    {
        $subjectCode = substr($this->getSubjectCode($course->subject), 0, 3);
        $examBoardCode = $this->getExamBoardCode($course->exam_board_id);
        $levelCode = substr($this->getLevelCode($course->level), 0, 1);
        $sequence = $this->getSequenceNumber($subjectCode, $examBoardCode, $levelCode);

        return $subjectCode . $examBoardCode . $levelCode . $sequence;
    }

    /**
     * Detailed format: SUBJ-EB-LEVEL-YEAR-001
     */
    protected function generateDetailedCode(Course $course): string
    {
        $subjectCode = $this->getSubjectCode($course->subject);
        $examBoardCode = $this->getExamBoardCode($course->exam_board_id);
        $levelCode = $this->getLevelCode($course->level);
        $yearCode = date('y'); // Current year (24 for 2024)
        $sequence = $this->getSequenceNumber($subjectCode, $examBoardCode, $levelCode, $yearCode);

        $parts = array_filter([$subjectCode, $examBoardCode, $levelCode, $yearCode, $sequence]);

        return implode('-', $parts);
    }

    /**
     * Extract subject code from subject name
     */
    protected function getSubjectCode(string $subject): string
    {
        // Remove special characters and spaces, take first 3 letters
        $cleanSubject = preg_replace('/[^a-zA-Z]/', '', $subject);
        return strtoupper(substr($cleanSubject, 0, 3));
    }

    /**
     * Get exam board code
     */
    protected function getExamBoardCode(?int $examBoardId): string
    {
        if (!$examBoardId) {
            return '';
        }

        $examBoard = ExamBoard::find($examBoardId);

        if (!$examBoard) {
            return '';
        }

        // Try to get code from exam board
        if ($examBoard->code) {
            return strtoupper(substr($examBoard->code, 0, 3));
        }

        // Generate from name if no code exists
        if ($examBoard->name) {
            $name = preg_replace('/[^a-zA-Z]/', '', $examBoard->name);
            return strtoupper(substr($name, 0, 3));
        }

        return '';
    }

    /**
     * Convert level to code
     */
    protected function getLevelCode(string $level): string
    {
        $levels = [
            'beginner' => 'BEG',
            'intermediate' => 'INT',
            'advanced' => 'ADV',
            'expert' => 'EXP',
            'foundation' => 'FND',
            'higher' => 'HIG',
            'standard' => 'STD',
            'extended' => 'EXT',
            'elementary' => 'ELE',
            'secondary' => 'SEC',
            'tertiary' => 'TER',
            'graduate' => 'GRD',
            'postgraduate' => 'PGD',
            'professional' => 'PRO',
            'certificate' => 'CERT',
            'diploma' => 'DIP',
            'degree' => 'DEG',
            'masters' => 'MSC',
            'phd' => 'PHD',
        ];

        $levelLower = strtolower($level);
        return $levels[$levelLower] ?? substr(strtoupper($level), 0, 3);
    }

    /**
     * Get the next sequence number
     */
    protected function getSequenceNumber(string $subjectCode, string $examBoardCode, string $levelCode, ?string $yearCode = null): string
    {
        // Count existing courses with the exact same parameters
        $count = Course::where('subject', 'like', $subjectCode . '%')
            ->when($examBoardCode, function ($query) use ($examBoardCode) {
                $examBoard = ExamBoard::where('code', 'like', $examBoardCode . '%')->first();
                return $query->where('exam_board_id', $examBoard?->id);
            })
            ->where('level', 'like', $levelCode . '%')
            ->when($yearCode, function ($query) use ($yearCode) {
                return $query->whereYear('created_at', '20' . $yearCode);
            })
            ->count();

        return str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    }
}
