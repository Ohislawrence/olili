<?php
// app/Http/Controllers/Student/CertificateController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Services\CertificateGenerationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class CertificateController extends Controller
{
    protected $certificateService;

    public function __construct(CertificateGenerationService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // Get filters
        $filters = $request->only(['search', 'status', 'sort']);

        // Build query
        $query = $user->certificates()
            ->with(['course', 'organization'])
            ->latest();

        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('certificate_number', 'like', "%{$filters['search']}%")
                  ->orWhere('title', 'like', "%{$filters['search']}%")
                  ->orWhereHas('course', function ($q) use ($filters) {
                      $q->where('title', 'like', "%{$filters['search']}%");
                  });
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'oldest':
                    $query->oldest();
                    break;
                case 'course':
                    $query->orderBy('title');
                    break;
                case 'expiring':
                    $query->orderBy('expiry_date', 'asc');
                    break;
                case 'newest':
                default:
                    $query->latest();
            }
        }

        $certificates = $query->paginate(12);

        // Enhance certificates with additional data
        $certificates->getCollection()->transform(function ($certificate) {
            // Get shareable image URL
            $certificate->image_url = $certificate->getShareableImageUrl();

            // Get PDF URL
            $certificate->pdf_url = $certificate->getPdfUrl();

            // Check if expired
            $certificate->is_expired = $certificate->isExpired();

            // Check if expiring soon (within 30 days)
            $certificate->is_expiring_soon = $certificate->expiry_date &&
                $certificate->expiry_date->isFuture() &&
                $certificate->expiry_date->diffInDays(now()) <= 30;

            // Days remaining
            if ($certificate->expiry_date) {
                $certificate->days_remaining = $certificate->expiry_date->isFuture()
                    ? $certificate->expiry_date->diffInDays(now())
                    : 0;
            }

            return $certificate;
        });

        // Calculate stats
        $stats = [
            'total' => $user->certificates()->count(),
            'active' => $user->certificates()->where('status', 'active')->count(),
            'expired' => $user->certificates()->where('status', 'expired')->count(),
            'total_downloads' => $user->certificates()->sum('download_count'),
            'recent_30d' => $user->certificates()
                ->where('issue_date', '>=', now()->subDays(30))
                ->count(),
            'expiring_soon' => $user->certificates()
                ->where('status', 'active')
                ->whereNotNull('expiry_date')
                ->where('expiry_date', '>', now())
                ->where('expiry_date', '<=', now()->addDays(30))
                ->count(),
        ];

        // Get statuses for filter
        $statuses = ['active', 'expired', 'revoked', 'pending'];

        return Inertia::render('Student/Certificates/Index', [
            'certificates' => $certificates,
            'stats' => $stats,
            'filters' => $filters,
            'statuses' => $statuses,
        ]);
    }

    public function show(Certificate $certificate)
    {
        // Authorization - ensure user owns the certificate
        if ($certificate->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $certificate->load(['course', 'organization', 'user']);

        // Get certificate data
        $certificateData = $certificate->getCertificateData();

        // Get shareable image URL
        $imageUrl = $certificate->getShareableImageUrl();

        // Get PDF URL
        $pdfUrl = $certificate->getPdfUrl();

        // Check eligibility for renewal
        $canRenew = $certificate->isExpired() &&
                   $certificate->status !== 'revoked' &&
                   $certificate->course->canEnroll(auth()->user());

        // Get activity log (if available)
        $activityLog = [];
        if ($certificate->metadata && isset($certificate->metadata['activity_log'])) {
            $activityLog = $certificate->metadata['activity_log'];
        }

        return Inertia::render('Student/Certificates/Show', [
            'certificate' => array_merge(
                $certificate->toArray(),
                [
                    'image_url' => $imageUrl,
                    'pdf_url' => $pdfUrl,
                    'is_expired' => $certificate->isExpired(),
                    'is_expiring_soon' => $certificate->expiry_date &&
                        $certificate->expiry_date->isFuture() &&
                        $certificate->expiry_date->diffInDays(now()) <= 30,
                    'days_remaining' => $certificate->expiry_date &&
                        $certificate->expiry_date->isFuture()
                        ? $certificate->expiry_date->diffInDays(now())
                        : 0,
                ]
            ),
            'certificate_data' => $certificateData,
            'can_download' => $certificate->canDownload(),
            'can_share' => $certificate->status === 'active' && $certificate->is_public,
            'can_renew' => $canRenew,
            'activity_log' => $activityLog,
        ]);
    }

    public function download(Certificate $certificate)
    {
        // Authorization
        if ($certificate->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if (!$certificate->canDownload()) {
            return redirect()->back()
                ->with('error', 'Certificate cannot be downloaded.');
        }

        // Get PDF path
        $pdfPath = $certificate->getPdfUrl();

        if (empty($pdfPath)) {
            // Generate PDF if not exists
            try {
                $pdfPath = $this->generatePdf($certificate);

                // Update metadata
                $metadata = $certificate->metadata ?? [];
                $metadata['pdf_path'] = $pdfPath;
                $certificate->update(['metadata' => $metadata]);
            } catch (\Exception $e) {
                \Log::error('Failed to generate PDF for download: ' . $e->getMessage());
                return redirect()->back()
                    ->with('error', 'Failed to generate PDF. Please try again.');
            }
        }

        // Increment download count
        $certificate->increment('download_count');

        // Return the PDF file
        $fileName = "certificate-{$certificate->certificate_number}.pdf";
        $filePath = storage_path('app/public/' . str_replace('/storage/', '', $pdfPath));

        return response()->download($filePath, $fileName);
    }

    public function downloadImage(Certificate $certificate)
    {
        // Authorization
        if ($certificate->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if (!$certificate->canDownload()) {
            return redirect()->back()
                ->with('error', 'Certificate image cannot be downloaded.');
        }

        // Get image path
        $imagePath = $certificate->getShareableImageUrl();

        if (empty($imagePath)) {
            // Generate image if not exists
            try {
                $imagePath = $this->certificateService->generateShareableImage($certificate);

                // Update metadata
                $metadata = $certificate->metadata ?? [];
                $metadata['image_path'] = $imagePath;
                $certificate->update(['metadata' => $metadata]);
            } catch (\Exception $e) {
                \Log::error('Failed to generate image for download: ' . $e->getMessage());
                return redirect()->back()
                    ->with('error', 'Failed to generate image. Please try again.');
            }
        }

        // Return the image file
        $fileName = "certificate-{$certificate->certificate_number}.png";
        $filePath = storage_path('app/public/' . str_replace('/storage/', '', $imagePath));

        return response()->download($filePath, $fileName);
    }

    public function share(Certificate $certificate, Request $request)
    {
        // Authorization
        if ($certificate->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if (!$certificate->canDownload()) {
            return response()->json([
                'success' => false,
                'message' => 'Certificate cannot be shared.',
            ], 403);
        }

        $request->validate([
            'platform' => 'required|in:linkedin,twitter,facebook,whatsapp,link,copy',
        ]);

        // Prepare sharing data
        $certificateData = $certificate->getCertificateData();

        $shareText = "I just completed '{$certificate->course->title}' on Olilearn! Check out my certificate: ";
        $shareUrl = $certificate->verification_url;
        $courseTitle = urlencode($certificate->course->title);
        $organizationName = urlencode($certificate->organization?->name ?? 'Olilearn AI Learning Platform');
        $issueYear = date('Y', strtotime($certificate->issue_date));

        $urls = [
            'linkedin' => "https://www.linkedin.com/profile/add?startTask=CERTIFICATION_NAME&name={$courseTitle}&organizationName={$organizationName}&issueYear={$issueYear}&certUrl=" . urlencode($shareUrl),
            'twitter' => "https://twitter.com/intent/tweet?text=" . urlencode($shareText) . "&url=" . urlencode($shareUrl),
            'facebook' => "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($shareUrl) . "&quote=" . urlencode($shareText),
            'whatsapp' => "https://wa.me/?text=" . urlencode($shareText . ' ' . $shareUrl),
            'link' => $shareUrl,
        ];

        if (in_array($request->platform, ['link', 'copy'])) {
            // Return URL for copying
            return response()->json([
                'success' => true,
                'url' => $shareUrl,
                'message' => 'Share link ready for copying',
            ]);
        }

        return response()->json([
            'success' => true,
            'url' => $urls[$request->platform],
            'message' => 'Redirecting to ' . ucfirst($request->platform),
        ]);
    }

    public function verify($hash)
    {
        // Try to find certificate by hash or certificate number
        $certificate = Certificate::where(function($query) use ($hash) {
                $query->where('verification_url', route('certificates.verify', $hash))
                      ->orWhere('certificate_number', $hash);
            })
            ->with(['user', 'course', 'organization'])
            ->first();

        if (!$certificate) {
            return Inertia::render('Public/CertificateVerification', [
                'error' => 'Certificate not found or invalid.',
                'is_valid' => false,
                'verification_date' => now()->format('F j, Y, g:i a'),
            ]);
        }

        $certificateData = $certificate->getCertificateData();
        $isValid = !$certificate->isExpired() && $certificate->status === 'active';

        // Increment view count if tracking is enabled
        if ($certificate->is_public) {
            $metadata = $certificate->metadata ?? [];
            $viewCount = isset($metadata['view_count']) ? $metadata['view_count'] + 1 : 1;
            $metadata['view_count'] = $viewCount;
            $metadata['last_verified_at'] = now()->toISOString();

            if (!isset($metadata['verification_history'])) {
                $metadata['verification_history'] = [];
            }

            $metadata['verification_history'][] = [
                'verified_at' => now()->toISOString(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ];

            $certificate->update(['metadata' => $metadata]);
        }

        return Inertia::render('Public/CertificateVerification', [
            'certificate' => $certificateData,
            'is_valid' => $isValid,
            'verification_date' => now()->format('F j, Y, g:i a'),
            'verification_id' => uniqid('VER-'),
            'view_count' => $certificate->metadata['view_count'] ?? 0,
        ]);
    }

    public function request()
    {
        $user = auth()->user();

        // Get completed courses from enrollments
        $completedCourses = CourseEnrollment::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with(['course' => function($query) {
                $query->with(['modules', 'capstoneProject']);
            }])
            ->get()
            ->map(function ($enrollment) use ($user) {
                $hasCertificate = Certificate::where('user_id', $user->id)
                    ->where('course_id', $enrollment->course_id)
                    ->where('status', 'active')
                    ->exists();

                // Check eligibility using the service
                $isEligible = $this->certificateService->isEligibleForCertificate($user, $enrollment->course);

                return [
                    'id' => $enrollment->course_id,
                    'title' => $enrollment->course->title,
                    'description' => $enrollment->course->description,
                    'progress_percentage' => $enrollment->progress_percentage,
                    'completed_at' => $enrollment->completed_at?->format('Y-m-d H:i:s'),
                    'has_certificate' => $hasCertificate,
                    'is_eligible' => $isEligible,
                    'requirements' => [
                        'progress_completed' => $enrollment->progress_percentage >= 100,
                        'modules_completed' => $enrollment->course->modules->where('is_completed', true)->count() === $enrollment->course->modules->count(),
                        'capstone_approved' => $enrollment->course->capstoneProject?->is_approved ?? false,
                    ],
                ];
            });

        // Get existing certificates for reference
        $certificates = $user->certificates()
            ->with('course')
            ->get(['id', 'course_id', 'certificate_number', 'issue_date']);

        return Inertia::render('Student/Certificates/Request', [
            'completedCourses' => $completedCourses,
            'certificates' => $certificates,
            'stats' => [
                'total_completed' => $completedCourses->count(),
                'eligible_certificates' => $completedCourses->where('is_eligible', true)->where('has_certificate', false)->count(),
                'existing_certificates' => $certificates->count(),
            ],
        ]);
    }

    public function requestCertificate(Course $course)
    {
        $user = auth()->user();

        // Check if user has completed enrollment for this course
        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'completed')
            ->first();

        if (!$enrollment) {
            return redirect()->back()
                ->with('error', 'You have not completed this course yet.');
        }

        // Check if certificate already exists
        $existing = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return redirect()->route('student.certificates.show', $existing->id)
                ->with('info', 'Certificate already exists.');
        }

        // Check eligibility using the service
        if (!$this->certificateService->isEligibleForCertificate($user, $course)) {
            return redirect()->back()
                ->with('error', 'You are not eligible for a certificate for this course. Please ensure all requirements are met.');
        }

        // Generate certificate
        try {
            $certificate = $this->certificateService->generateCertificate($user, $course);

            return redirect()->route('student.certificates.show', $certificate->id)
                ->with('success', 'Certificate generated successfully!');

        } catch (\Exception $e) {
            \Log::error('Failed to generate certificate: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to generate certificate: ' . $e->getMessage());
        }
    }

    public function exportAll()
    {
        $user = auth()->user();
        $certificates = $user->certificates()
            ->where('status', 'active')
            ->with('course')
            ->get();

        if ($certificates->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No certificates available for export.');
        }

        // Create temporary directory
        $tempDir = storage_path('app/temp/certificates-' . $user->id . '-' . now()->timestamp);
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $zipFileName = "certificates-{$user->id}-" . now()->format('Y-m-d') . '.zip';
        $zipPath = storage_path("app/public/temp/{$zipFileName}");

        // Ensure temp directory exists
        Storage::disk('public')->makeDirectory('temp');

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($certificates as $certificate) {
                // Get PDF path
                $pdfPath = $certificate->getPdfUrl();

                if ($pdfPath) {
                    $filePath = storage_path('app/public/' . str_replace('/storage/', '', $pdfPath));
                    if (file_exists($filePath)) {
                        $zip->addFile($filePath, "{$certificate->certificate_number}.pdf");
                    }
                }
            }

            $zip->close();
        } else {
            return redirect()->back()
                ->with('error', 'Failed to create export file.');
        }

        // Clean up temp directory
        if (file_exists($tempDir)) {
            array_map('unlink', glob("$tempDir/*"));
            rmdir($tempDir);
        }

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    public function renew(Request $request, Certificate $certificate)
    {
        // Authorization
        if ($certificate->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if (!$certificate->isExpired()) {
            return redirect()->back()
                ->with('error', 'Certificate is not expired.');
        }

        if ($certificate->status === 'revoked') {
            return redirect()->back()
                ->with('error', 'Revoked certificates cannot be renewed.');
        }

        // Check if user can still access the course
        $enrollment = CourseEnrollment::where('user_id', auth()->id())
            ->where('course_id', $certificate->course_id)
            ->first();

        if (!$enrollment || $enrollment->status !== 'completed') {
            return redirect()->back()
                ->with('error', 'You no longer have access to this course.');
        }

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            // Update expiry date (2 years from now)
            $certificate->update([
                'expiry_date' => now()->addYears(2),
                'status' => 'active',
            ]);

            // Log renewal in metadata
            $metadata = $certificate->metadata ?? [];
            if (!isset($metadata['renewals'])) {
                $metadata['renewals'] = [];
            }

            $metadata['renewals'][] = [
                'renewed_at' => now()->toISOString(),
                'reason' => $request->reason,
                'new_expiry' => now()->addYears(2)->toISOString(),
            ];

            $certificate->update(['metadata' => $metadata]);

            return redirect()->route('student.certificates.show', $certificate->id)
                ->with('success', 'Certificate renewed successfully for 2 years.');

        } catch (\Exception $e) {
            \Log::error('Failed to renew certificate: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to renew certificate: ' . $e->getMessage());
        }
    }

    public function togglePublic(Certificate $certificate)
    {
        // Authorization
        if ($certificate->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        try {
            $certificate->update([
                'is_public' => !$certificate->is_public,
            ]);

            return redirect()->back()
                ->with('success', 'Certificate visibility updated.');

        } catch (\Exception $e) {
            \Log::error('Failed to toggle certificate visibility: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update visibility.');
        }
    }

    private function generatePdf(Certificate $certificate): string
    {
        $data = $certificate->getCertificateData();

        $pdf = Pdf::loadView('certificates.pdf', [
            'certificate' => $data,
        ]);

        $fileName = "certificates/pdf/{$certificate->certificate_number}.pdf";
        $pdfPath = storage_path("app/public/{$fileName}");

        // Ensure directory exists
        Storage::disk('public')->makeDirectory('certificates/pdf');

        $pdf->save($pdfPath);

        return Storage::url($fileName);
    }
}
