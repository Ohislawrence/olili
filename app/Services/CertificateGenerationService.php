<?php
// app/Services/CertificateGenerationService.php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use App\Models\OrganizationProfile;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class CertificateGenerationService
{
    protected $imageManager;
    protected $defaultTemplate;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
        $this->defaultTemplate = CertificateTemplate::where('is_default', true)->first();
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
        $certificate->update(['metadata' => ['pdf_path' => $pdfPath]]);

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
            'student' => [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar_url,
            ],
            'course' => [
                'title' => $course->title,
                'subject' => $course->subject,
                'level' => $course->level,
                'completed_date' => $course->actual_completion_date->format('F j, Y'),
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
     * Generate shareable image
     */
    public function generateShareableImage(Certificate $certificate): string
    {
        $data = $certificate->getCertificateData();
        
        // Create image using Intervention Image
        $image = $this->imageManager->create(1200, 800);
        
        // Set background
        $backgroundColor = $certificate->organization?->certificateTemplate?->background_color ?? '#ffffff';
        $image->fill($backgroundColor);
        
        // Add logo
        $logoPath = $data['issuer']['logo'] ?? public_path('images/logo.png');
        if (file_exists($logoPath)) {
            $logo = $this->imageManager->read($logoPath);
            $logo->scale(200, 200);
            $image->place($logo, 'top-center', 10, 50);
        }
        
        // Add text
        $textColor = $certificate->organization?->certificateTemplate?->text_color ?? '#000000';
        
        // Certificate title
        $image->text($data['title'], 600, 300, function($font) use ($textColor) {
            $font->file(public_path('fonts/Roboto-Bold.ttf'));
            $font->size(48);
            $font->color($textColor);
            $font->align('center');
        });
        
        // Student name
        $image->text($data['student_name'], 600, 400, function($font) use ($textColor) {
            $font->file(public_path('fonts/Roboto-Regular.ttf'));
            $font->size(36);
            $font->color($textColor);
            $font->align('center');
        });
        
        // Course title
        $image->text("has successfully completed", 600, 450, function($font) use ($textColor) {
            $font->file(public_path('fonts/Roboto-Italic.ttf'));
            $font->size(24);
            $font->color($textColor);
            $font->align('center');
        });
        
        $image->text($data['course_title'], 600, 500, function($font) use ($textColor) {
            $font->file(public_path('fonts/Roboto-Bold.ttf'));
            $font->size(28);
            $font->color($textColor);
            $font->align('center');
        });
        
        // Issue date
        $image->text("Issued on: {$data['issue_date']}", 600, 600, function($font) use ($textColor) {
            $font->file(public_path('fonts/Roboto-Regular.ttf'));
            $font->size(18);
            $font->color($textColor);
            $font->align('center');
        });
        
        // Certificate number
        $image->text("Certificate #: {$data['certificate_number']}", 600, 650, function($font) use ($textColor) {
            $font->file(public_path('fonts/Roboto-Regular.ttf'));
            $font->size(16);
            $font->color($textColor);
            $font->align('center');
        });
        
        // Add QR code
        if ($certificate->qr_code) {
            $qrCodePath = storage_path('app/public/' . str_replace('/storage/', '', $certificate->qr_code));
            if (file_exists($qrCodePath)) {
                $qrCode = $this->imageManager->read($qrCodePath);
                $qrCode->scale(100, 100);
                $image->place($qrCode, 'bottom-right', 50, 50);
            }
        }
        
        // Save image
        $fileName = "certificates/images/{$certificate->certificate_number}.png";
        $imagePath = storage_path("app/public/{$fileName}");
        
        Storage::disk('public')->makeDirectory('certificates/images');
        $image->save($imagePath);
        
        return Storage::url($fileName);
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
}