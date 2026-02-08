<?php
// app/Services/CertificateGenerationService.php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use App\Models\OrganizationProfile;
use App\Models\CertificateTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class CertificateGenerationService
{
    protected $defaultTemplate;
    protected $imageService;

    public function __construct()
    {
        $this->defaultTemplate = CertificateTemplate::where('is_default', true)->first();
        $this->imageService = new CertificateImageService();
    }

    /**
     * Check if a user is eligible for a certificate
     */
    public function isEligibleForCertificate(User $user, Course $course): bool
    {
        // Check if course is completed
        if ($course->status !== 'completed') {
            return false;
        }

        // Check if all modules are completed
        $totalModules = $course->modules()->count();
        $completedModules = $course->modules()->where('is_completed', true)->count();

        if ($totalModules !== $completedModules) {
            return false;
        }

        // Check if capstone project is completed and approved
        $capstone = $course->capstoneProject;
        if (!$capstone || !$capstone->is_approved) {
            return false;
        }

        // Check if user already has a certificate for this course
        $existingCertificate = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->first();

        return !$existingCertificate;
    }

    /**
     * Generate certificate for a completed course
     */
    public function generateCertificate(User $user, Course $course, ?OrganizationProfile $organization = null): Certificate
    {
        // Check eligibility
        if (!$this->isEligibleForCertificate($user, $course)) {
            throw new \Exception('User is not eligible for certificate');
        }

        // Determine issuer
        $issuer = $organization ?? OrganizationProfile::where('name', 'Olilearn')->first();

        // Create certificate
        $certificate = Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'organization_id' => $organization?->id,
            'issued_by_type' => $organization ? OrganizationProfile::class : User::class,
            'issued_by_id' => $organization ? $organization->id : 1, // Admin user ID
            'title' => "Certificate of Completion - {$course->title}",
            'description' => "This certifies that {$user->name} has successfully completed the course '{$course->title}'.",
            'issue_date' => now(),
            'expiry_date' => now()->addYears(2), // Certificates valid for 2 years
            'status' => 'active',
            'is_public' => true,
        ]);

        // Generate certificate number and verification URL
        $certificate->generateCertificateNumber();
        $certificate->generateVerificationUrl();

        // Generate QR code
        $this->generateQrCode($certificate);

        // Save certificate data
        $certificateData = $this->prepareCertificateData($certificate, $course, $user, $organization);
        $certificate->update(['certificate_data' => $certificateData]);

        // Generate PDF
        $pdfPath = $this->generatePdf($certificate);

        // Generate shareable image
        $imagePath = $this->generateShareableImage($certificate);

        $certificate->update([
            'metadata' => [
                'pdf_path' => $pdfPath,
                'image_path' => $imagePath,
                'generated_at' => now()->toISOString(),
            ]
        ]);

        // Send notification to user
        $this->sendCertificateNotification($user, $certificate);

        return $certificate->fresh();
    }

    /**
     * Prepare certificate data
     */
    protected function prepareCertificateData(Certificate $certificate, Course $course, User $user, ?OrganizationProfile $organization): array
    {
        $template = $organization?->certificateTemplate ?? $this->defaultTemplate;

        return [
            'certificate_number' => $certificate->certificate_number,
            'student_name' => $user->name,
            'student_email' => $user->email,
            'course_title' => $course->title,
            'title' => $certificate->title,
            'issue_date' => $certificate->issue_date->format('F j, Y'),
            'expiry_date' => $certificate->expiry_date ? $certificate->expiry_date->format('F j, Y') : null,
            'verification_url' => $certificate->verification_url,
            'qr_code' => $certificate->qr_code,
            'student' => [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar_url,
            ],
            'course' => [
                'title' => $course->title,
                'subject' => $course->subject,
                'level' => $course->level,
                'completed_date' => $course->actual_completion_date?->format('F j, Y'),
                'progress_percentage' => $course->progress_percentage,
            ],
            'issuer' => [
                'name' => $organization?->name ?? 'Olilearn AI Learning Platform',
                'logo' => $organization?->logo ?? config('app.logo'),
                'website' => $organization?->website ?? config('app.url'),
            ],
            'completion_details' => [
                'modules_completed' => $course->modules()->where('is_completed', true)->count(),
                'total_modules' => $course->modules()->count(),
                'total_hours' => $course->actual_duration_hours,
                'capstone_score' => $course->capstoneProject?->final_score ?? 100,
            ],
            'template' => $template?->getLayoutConfig() ?? [],
            'achievements' => $this->getCourseAchievements($course, $user),
        ];
    }

    /**
     * Generate QR code for certificate verification
     */
    protected function generateQrCode(Certificate $certificate): void
    {
        $qrCode = QrCode::format('png')
            ->size(300)
            ->generate($certificate->verification_url);

        $fileName = "certificates/qr_codes/{$certificate->certificate_number}.png";
        Storage::disk('public')->put($fileName, $qrCode);

        $certificate->update(['qr_code' => Storage::url($fileName)]);
    }

    /**
     * Generate PDF certificate
     */
    protected function generatePdf(Certificate $certificate): string
    {
        $data = $certificate->getCertificateData();

        $pdf = Pdf::loadView('certificates.pdf', [
            'certificate' => $data,
            'template' => $certificate->organization?->certificateTemplate ?? $this->defaultTemplate,
        ]);

        $fileName = "certificates/pdf/{$certificate->certificate_number}.pdf";
        $pdfPath = storage_path("app/public/{$fileName}");

        // Ensure directory exists
        Storage::disk('public')->makeDirectory('certificates/pdf');

        $pdf->save($pdfPath);

        return Storage::url($fileName);
    }

    /**
     * Generate shareable image using GD
     */
    public function generateShareableImage(Certificate $certificate): string
    {
        $data = $certificate->getCertificateData();

        // Get template colors or use defaults
        $backgroundColor = '#ffffff';
        $textColor = '#000000';

        if ($certificate->organization?->certificateTemplate) {
            $backgroundColor = $certificate->organization->certificateTemplate->background_color ?? $backgroundColor;
            $textColor = $certificate->organization->certificateTemplate->text_color ?? $textColor;
        } elseif ($this->defaultTemplate) {
            $backgroundColor = $this->defaultTemplate->background_color ?? $backgroundColor;
            $textColor = $this->defaultTemplate->text_color ?? $textColor;
        }

        // Generate the image using GD
        $imageUrl = $this->imageService->createCertificateImage($data, $backgroundColor, $textColor);

        return $imageUrl;
    }

    /**
     * Get course achievements
     */
    protected function getCourseAchievements(Course $course, User $user): array
    {
        $achievements = [];

        // Check for high score achievement
        if ($course->progress_percentage >= 90) {
            $achievements[] = [
                'title' => 'High Achiever',
                'description' => 'Scored 90% or higher in the course',
                'icon' => '🏆',
            ];
        }

        // Check for fast completion
        $estimatedDuration = $course->estimated_duration_hours;
        $actualDuration = $course->actual_duration_hours;

        if ($actualDuration && $actualDuration < ($estimatedDuration * 0.7)) {
            $achievements[] = [
                'title' => 'Fast Learner',
                'description' => 'Completed the course 30% faster than estimated',
                'icon' => '⚡',
            ];
        }

        // Check for perfect quiz scores
        $perfectQuizzes = $course->quizzes()
            ->whereHas('attempts', function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('percentage', 100);
            })
            ->count();

        if ($perfectQuizzes >= 3) {
            $achievements[] = [
                'title' => 'Quiz Master',
                'description' => 'Aced 3 or more quizzes with perfect scores',
                'icon' => '🧠',
            ];
        }

        return $achievements;
    }

    /**
     * Send certificate notification
     */
    protected function sendCertificateNotification(User $user, Certificate $certificate): void
    {
        $user->notify(new \App\Notifications\CertificateIssuedNotification($certificate));

        // Also notify organization if applicable
        if ($certificate->organization) {
            $organizationUser = $certificate->organization->user;
            $organizationUser->notify(new \App\Notifications\StudentCertificateIssuedNotification($certificate));
        }
    }

    /**
     * Batch generate certificates for organization
     */
    public function batchGenerateForOrganization(OrganizationProfile $organization, array $courseIds): array
    {
        $results = [
            'successful' => [],
            'failed' => [],
        ];

        $students = $organization->students()
            ->whereHas('courses', function($q) use ($courseIds) {
                $q->whereIn('courses.id', $courseIds)
                  ->where('status', 'completed');
            })
            ->get();

        foreach ($students as $student) {
            foreach ($courseIds as $courseId) {
                $course = Course::find($courseId);

                if ($course && $this->isEligibleForCertificate($student->user, $course)) {
                    try {
                        $certificate = $this->generateCertificate($student->user, $course, $organization);
                        $results['successful'][] = [
                            'student' => $student->user->name,
                            'course' => $course->title,
                            'certificate_number' => $certificate->certificate_number,
                        ];
                    } catch (\Exception $e) {
                        $results['failed'][] = [
                            'student' => $student->user->name,
                            'course' => $course->title,
                            'error' => $e->getMessage(),
                        ];
                    }
                }
            }
        }

        return $results;
    }

    /**
     * Regenerate certificate image (useful if template changes)
     */
    public function regenerateCertificateImage(Certificate $certificate): bool
    {
        try {
            $data = $certificate->getCertificateData();

            // Get template colors
            $backgroundColor = '#ffffff';
            $textColor = '#000000';

            if ($certificate->organization?->certificateTemplate) {
                $backgroundColor = $certificate->organization->certificateTemplate->background_color ?? $backgroundColor;
                $textColor = $certificate->organization->certificateTemplate->text_color ?? $textColor;
            } elseif ($this->defaultTemplate) {
                $backgroundColor = $this->defaultTemplate->background_color ?? $backgroundColor;
                $textColor = $this->defaultTemplate->text_color ?? $textColor;
            }

            // Generate new image
            $imageUrl = $this->imageService->createCertificateImage($data, $backgroundColor, $textColor);

            // Update metadata
            $metadata = $certificate->metadata ?? [];
            $metadata['image_path'] = $imageUrl;
            $metadata['regenerated_at'] = now()->toISOString();

            $certificate->update(['metadata' => $metadata]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to regenerate certificate image: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate certificate preview for a template
     */
    public function generateTemplatePreview(CertificateTemplate $template, array $sampleData = []): string
    {
        // Prepare sample data if not provided
        if (empty($sampleData)) {
            $sampleData = [
                'certificate_number' => 'OLCERT-' . date('Y') . '-000001',
                'student_name' => 'John Doe',
                'course_title' => 'Sample Course',
                'title' => 'Certificate of Completion',
                'issue_date' => date('F j, Y'),
                'expiry_date' => date('F j, Y', strtotime('+2 years')),
                'verification_url' => 'https://example.com/verify/123456',
                'qr_code' => null,
                'issuer' => [
                    'name' => 'Olilearn AI Learning Platform',
                    'logo' => config('app.logo'),
                ],
            ];
        }

        // Generate preview image
        $imageUrl = $this->imageService->createCertificateImage(
            $sampleData,
            $template->background_color ?? '#ffffff',
            $template->text_color ?? '#000000'
        );

        return $imageUrl;
    }
}
