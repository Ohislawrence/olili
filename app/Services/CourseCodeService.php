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
        // Remove special characters and numbers
        $cleanSubject = preg_replace('/[^a-zA-Z\s]/', '', $subject);

        // Common subject abbreviations
        $subjectAbbreviations = [
            'mathematics' => 'MATH',
            'physics' => 'PHYS',
            'chemistry' => 'CHEM',
            'biology' => 'BIOL',
            'english' => 'ENG',
            'literature' => 'LIT',
            'history' => 'HIST',
            'geography' => 'GEOG',
            'economics' => 'ECON',
            'accounting' => 'ACCT',
            'business' => 'BUS',
            'computer' => 'COMP',
            'programming' => 'PROG',
            'science' => 'SCI',
            'data' => 'DATA',
            'artificial' => 'AI',
            'web' => 'WEB',
            'mobile' => 'MOB',
            'graphic' => 'GRAPH',
            'music' => 'MUS',
            'art' => 'ART',
        ];

        $subjectLower = strtolower($cleanSubject);

        // Check for exact matches first
        if (isset($subjectAbbreviations[$subjectLower])) {
            return $subjectAbbreviations[$subjectLower];
        }

        // Check for partial matches
        foreach ($subjectAbbreviations as $key => $abbr) {
            if (str_contains($subjectLower, $key)) {
                return $abbr;
            }
        }

        // Default: use first 4 letters
        return strtoupper(substr($cleanSubject, 0, 4));
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
        $query = Course::query();

        // Build search pattern
        $pattern = $subjectCode;
        if ($examBoardCode) {
            $pattern .= '-' . $examBoardCode;
        }
        $pattern .= '-' . $levelCode;

        if ($yearCode) {
            $pattern .= '-' . $yearCode;
        }

        $pattern .= '-%';

        // Find highest existing sequence
        $existingCodes = $query->where('code', 'like', $pattern)
            ->pluck('code')
            ->map(function ($code) use ($pattern) {
                // Extract sequence number from code
                $baseLength = strlen(str_replace('%', '', $pattern)) - 3; // -3 for the placeholder
                $sequencePart = substr($code, $baseLength + 1); // +1 for the dash
                return intval($sequencePart);
            })
            ->filter()
            ->toArray();

        $nextSequence = empty($existingCodes) ? 1 : max($existingCodes) + 1;

        return str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
    }
}
