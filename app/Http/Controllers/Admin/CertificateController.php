<?php
// app/Http/Controllers/Admin/CertificateController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\User;
use App\Models\Course;
use App\Models\OrganizationProfile;
use App\Services\CertificateGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;

class CertificateController extends Controller
{
    protected $certificateService;

    public function __construct(CertificateGenerationService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    public function create(Request $request)
    {
        $user = User::findOrFail($request->user_id);

        $completedCourses = $user->courseEnrollments()
            ->where('status', 'completed')
            ->with(['course', 'course.modules', 'course.capstoneProject'])
            ->get()
            ->map(function ($enrollment) use ($user) {
                $hasCertificate = Certificate::where('user_id', $user->id)
                    ->where('course_id', $enrollment->course_id)
                    ->where('status', 'active')
                    ->exists();

                // Check capstone approval
                $capstoneApproved = $enrollment->course->capstoneProject?->is_approved ?? false;

                // Check if all modules are completed
                $totalModules = $enrollment->course->modules->count();
                $completedModules = $enrollment->course->modules->where('is_completed', true)->count();

                // Determine if eligible
                $canGenerate = !$hasCertificate &&
                            $enrollment->progress_percentage >= 100 &&
                            $completedModules === $totalModules &&
                            $capstoneApproved;

                return [
                    'id' => $enrollment->course_id,
                    'title' => $enrollment->course->title,
                    'description' => $enrollment->course->description,
                    'progress_percentage' => $enrollment->progress_percentage,
                    'completed_at' => $enrollment->completed_at?->format('Y-m-d'),
                    'has_certificate' => $hasCertificate,
                    'can_generate' => $canGenerate,
                    'modules_completed' => $completedModules,
                    'total_modules' => $totalModules,
                    'capstone_approved' => $capstoneApproved,
                ];
            });

        $organizations = OrganizationProfile::all();

        return Inertia::render('Admin/Certificates/Generate', [
            'user' => $user->only(['id', 'name', 'email', 'avatar_url']),
            'completedCourses' => $completedCourses,
            'organizations' => $organizations->map(function ($org) {
                return [
                    'id' => $org->id,
                    'name' => $org->name,
                    'is_verified' => $org->is_verified,
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'organization_id' => 'nullable|exists:organization_profiles,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'status' => 'required|in:active,pending',
            'is_public' => 'boolean',
            'send_notification' => 'boolean',
            'generate_pdf' => 'boolean',
            'generate_image' => 'boolean',
        ]);

        $user = User::findOrFail($request->user_id);
        $course = Course::findOrFail($request->course_id);
        $organization = $request->organization_id
            ? OrganizationProfile::find($request->organization_id)
            : null;

        try {
            // Check if certificate already exists
            $existing = Certificate::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->where('status', 'active')
                ->first();

            if ($existing) {
                return redirect()->back()
                    ->with('error', 'Certificate already exists for this course.');
            }

            // Create certificate
            $certificate = Certificate::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'organization_id' => $organization?->id,
                'issued_by_type' => $organization ? OrganizationProfile::class : User::class,
                'issued_by_id' => $organization ? $organization->id : 1,
                'title' => $request->title,
                'description' => $request->description,
                'issue_date' => $request->issue_date,
                'expiry_date' => $request->expiry_date,
                'status' => $request->status,
                'is_public' => $request->is_public ?? true,
            ]);

            // Generate certificate number and verification URL
            $certificate->generateCertificateNumber();
            $certificate->generateVerificationUrl();

            // Generate QR code
            $this->generateQrCode($certificate);

            // Save certificate data
            $certificateData = $this->prepareCertificateData($certificate, $course, $user, $organization);
            $certificate->update(['certificate_data' => $certificateData]);

            // Generate PDF if requested
            if ($request->generate_pdf) {
                $pdfPath = $this->generatePdf($certificate);
            }

            // Generate image if requested
            if ($request->generate_image) {
                $imagePath = $this->generateShareableImage($certificate);
            }

            // Update metadata
            $metadata = [
                'pdf_generated' => $request->generate_pdf,
                'image_generated' => $request->generate_image,
                'generated_at' => now()->toISOString(),
            ];

            if (isset($pdfPath)) {
                $metadata['pdf_path'] = $pdfPath;
            }

            if (isset($imagePath)) {
                $metadata['image_path'] = $imagePath;
            }

            $certificate->update(['metadata' => $metadata]);

            // Send notification if requested
            if ($request->send_notification) {
                $user->notify(new \App\Notifications\CertificateIssuedNotification($certificate));
            }

            return redirect()->route('admin.users.certificates', $user->id)
                ->with('success', 'Certificate generated successfully!');

        } catch (\Exception $e) {
            \Log::error('Failed to generate certificate: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to generate certificate: ' . $e->getMessage());
        }
    }

    public function show(Certificate $certificate)
    {
        $certificate->load([
            'user' => function ($query) {
                $query->select(['id', 'name', 'email', 'avatar_url', 'created_at']);
            },
            'course' => function ($query) {
                $query->select(['id', 'title', 'description', 'subject', 'level', 'estimated_duration_hours']);
            },
            'organization' => function ($query) {
                $query->select(['id', 'name', 'logo', 'website', 'is_verified']);
            },
        ]);

        // Get certificate data
        $certificateData = $certificate->getCertificateData();

        // Get activity log (you need to create this relationship in Certificate model)
        $activityLog = $certificate->activities()
            ->with('user:id,name')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'type' => $activity->type,
                    'description' => $activity->description,
                    'created_at' => $activity->created_at,
                    'user' => $activity->user,
                ];
            });

        // Calculate verification status
        $verificationStatus = 'valid';
        if ($certificate->isExpired()) {
            $verificationStatus = 'expired';
        } elseif ($certificate->status === 'revoked') {
            $verificationStatus = 'revoked';
        }

        return Inertia::render('Admin/Certificates/Show', [
            'certificate' => array_merge(
                $certificate->toArray(),
                [
                    'completion_data' => $certificateData['completion_data'] ?? null,
                    'image_url' => $certificate->getShareableImageUrl(),
                    'pdf_url' => $certificate->getPdfUrl(),
                    'issued_by' => $certificateData['issued_by'] ?? null,
                    'issuer' => $certificateData['issuer'] ?? null,
                    'verification_status' => $verificationStatus,
                    'activity_log' => $activityLog,
                    'view_count' => $certificate->view_count ?? 0,
                    'generated_by' => auth()->user()->name,
                    'last_verified_at' => $certificate->last_verified_at,
                ]
            ),
        ]);
    }

    public function updateStatus(Request $request, Certificate $certificate)
    {
        $request->validate([
            'status' => 'required|in:active,expired,revoked,pending',
            'reason' => 'nullable|string|max:500',
        ]);

        $oldStatus = $certificate->status;
        $certificate->update(['status' => $request->status]);

        // Log status change
        $metadata = $certificate->metadata ?? [];
        $metadata['status_changes'][] = [
            'from' => $oldStatus,
            'to' => $request->status,
            'reason' => $request->reason,
            'changed_by' => auth()->id(),
            'changed_at' => now()->toISOString(),
        ];

        $certificate->update(['metadata' => $metadata]);

        return redirect()->back()
            ->with('success', "Certificate status updated from '{$oldStatus}' to '{$request->status}'.");
    }

    public function renew(Request $request, Certificate $certificate)
    {
        $request->validate([
            'expiry_date' => 'required|date|after:today',
            'reason' => 'nullable|string|max:500',
        ]);

        $oldExpiry = $certificate->expiry_date;
        $certificate->update([
            'expiry_date' => $request->expiry_date,
            'status' => 'active',
        ]);

        // Log renewal
        $metadata = $certificate->metadata ?? [];
        $metadata['renewals'][] = [
            'old_expiry' => $oldExpiry,
            'new_expiry' => $request->expiry_date,
            'reason' => $request->reason,
            'renewed_by' => auth()->id(),
            'renewed_at' => now()->toISOString(),
        ];

        $certificate->update(['metadata' => $metadata]);

        return redirect()->back()
            ->with('success', 'Certificate renewed successfully.');
    }

    public function send(Request $request, Certificate $certificate)
    {
        $request->validate([
            'method' => 'required|in:email,notification,both',
            'message' => 'nullable|string|max:1000',
        ]);

        try {
            $user = $certificate->user;

            if (in_array($request->method, ['email', 'both'])) {
                $user->notify(new \App\Notifications\CertificateIssuedNotification($certificate, $request->message));
            }

            if (in_array($request->method, ['notification', 'both'])) {
                $user->notifications()->create([
                    'type' => 'certificate_issued',
                    'data' => [
                        'certificate_id' => $certificate->id,
                        'certificate_number' => $certificate->certificate_number,
                        'course_title' => $certificate->course->title,
                        'message' => $request->message,
                    ],
                    'read_at' => null,
                ]);
            }

            return redirect()->back()
                ->with('success', 'Certificate sent successfully.');

        } catch (\Exception $e) {
            \Log::error('Failed to send certificate: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to send certificate: ' . $e->getMessage());
        }
    }

    public function regenerateImage(Certificate $certificate)
    {
        try {
            $success = $this->certificateService->regenerateCertificateImage($certificate);

            if ($success) {
                return redirect()->back()
                    ->with('success', 'Certificate image regenerated successfully!');
            } else {
                return redirect()->back()
                    ->with('error', 'Failed to regenerate certificate image.');
            }
        } catch (\Exception $e) {
            \Log::error('Failed to regenerate certificate image: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to regenerate certificate image: ' . $e->getMessage());
        }
    }

    public function download(Certificate $certificate)
    {
        $pdfPath = $certificate->getPdfUrl();

        if (empty($pdfPath)) {
            try {
                $pdfPath = $this->generatePdf($certificate);

                $metadata = $certificate->metadata ?? [];
                $metadata['pdf_path'] = $pdfPath;
                $certificate->update(['metadata' => $metadata]);
            } catch (\Exception $e) {
                \Log::error('Failed to generate PDF: ' . $e->getMessage());
                return redirect()->back()
                    ->with('error', 'Failed to generate PDF: ' . $e->getMessage());
            }
        }

        $certificate->increment('download_count');

        $fileName = "certificate-{$certificate->certificate_number}.pdf";
        $filePath = storage_path('app/public/' . str_replace('/storage/', '', $pdfPath));

        return response()->download($filePath, $fileName);
    }

    public function downloadImage(Certificate $certificate)
    {
        $imagePath = $certificate->getShareableImageUrl();

        if (empty($imagePath)) {
            try {
                $imagePath = $this->generateShareableImage($certificate);

                $metadata = $certificate->metadata ?? [];
                $metadata['image_path'] = $imagePath;
                $certificate->update(['metadata' => $metadata]);
            } catch (\Exception $e) {
                \Log::error('Failed to generate image: ' . $e->getMessage());
                return redirect()->back()
                    ->with('error', 'Failed to generate image: ' . $e->getMessage());
            }
        }

        $fileName = "certificate-{$certificate->certificate_number}.png";
        $filePath = storage_path('app/public/' . str_replace('/storage/', '', $imagePath));

        return response()->download($filePath, $fileName);
    }

    public function destroy(Certificate $certificate)
    {
        try {
            // Delete associated files
            $this->deleteCertificateFiles($certificate);

            $certificateNumber = $certificate->certificate_number;
            $userId = $certificate->user_id;
            $certificate->delete();

            return redirect()->route('admin.users.certificates', $userId)
                ->with('success', "Certificate {$certificateNumber} deleted successfully.");

        } catch (\Exception $e) {
            \Log::error('Failed to delete certificate: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete certificate: ' . $e->getMessage());
        }
    }

    public function batchGenerate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_ids' => 'required|array|min:1',
            'course_ids.*' => 'exists:courses,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $results = [
            'successful' => [],
            'failed' => [],
        ];

        foreach ($request->course_ids as $courseId) {
            $course = Course::find($courseId);

            try {
                // Check eligibility
                if (!$this->certificateService->isEligibleForCertificate($user, $course)) {
                    $results['failed'][] = [
                        'course' => $course->title,
                        'error' => 'User is not eligible for certificate',
                    ];
                    continue;
                }

                // Check if certificate already exists
                $existing = Certificate::where('user_id', $user->id)
                    ->where('course_id', $course->id)
                    ->where('status', 'active')
                    ->first();

                if ($existing) {
                    $results['failed'][] = [
                        'course' => $course->title,
                        'error' => 'Certificate already exists',
                    ];
                    continue;
                }

                // Generate certificate
                $certificate = $this->certificateService->generateCertificate($user, $course);

                $results['successful'][] = [
                    'course' => $course->title,
                    'certificate_number' => $certificate->certificate_number,
                ];

            } catch (\Exception $e) {
                $results['failed'][] = [
                    'course' => $course->title ?? 'Unknown',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return redirect()->route('admin.users.certificates', $user->id)
            ->with('success', "Generated " . count($results['successful']) . " certificates successfully.")
            ->with('batch_results', $results);
    }

    public function checkEligible(User $user)
    {
        $eligibleCourses = [];

        $enrollments = $user->courseEnrollments()
            ->where('status', 'completed')
            ->with('course')
            ->get();

        foreach ($enrollments as $enrollment) {
            $hasCertificate = Certificate::where('user_id', $user->id)
                ->where('course_id', $enrollment->course_id)
                ->where('status', 'active')
                ->exists();

            if (!$hasCertificate && $this->certificateService->isEligibleForCertificate($user, $enrollment->course)) {
                $eligibleCourses[] = [
                    'id' => $enrollment->course_id,
                    'title' => $enrollment->course->title,
                    'completed_at' => $enrollment->completed_at->format('Y-m-d'),
                    'progress_percentage' => $enrollment->progress_percentage,
                ];
            }
        }

        return response()->json([
            'eligible_courses' => $eligibleCourses,
        ]);
    }

    public function bulkSend(Request $request)
    {
        $request->validate([
            'certificate_ids' => 'required|array|min:1',
            'certificate_ids.*' => 'exists:certificates,id',
        ]);

        $certificates = Certificate::whereIn('id', $request->certificate_ids)
            ->with('user')
            ->get();

        $sentCount = 0;
        foreach ($certificates as $certificate) {
            try {
                $certificate->user->notify(new \App\Notifications\CertificateIssuedNotification($certificate));
                $sentCount++;
            } catch (\Exception $e) {
                \Log::error('Failed to send certificate ' . $certificate->id . ': ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Sent {$sentCount} certificates successfully.",
        ]);
    }

    public function bulkRegenerate(Request $request)
    {
        $request->validate([
            'certificate_ids' => 'required|array|min:1',
            'certificate_ids.*' => 'exists:certificates,id',
        ]);

        $successCount = 0;
        $failedCount = 0;

        foreach ($request->certificate_ids as $certificateId) {
            try {
                $certificate = Certificate::find($certificateId);
                $success = $this->certificateService->regenerateCertificateImage($certificate);

                if ($success) {
                    $successCount++;
                } else {
                    $failedCount++;
                }
            } catch (\Exception $e) {
                \Log::error('Failed to regenerate certificate ' . $certificateId . ': ' . $e->getMessage());
                $failedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Regenerated {$successCount} images successfully. {$failedCount} failed.",
        ]);
    }

    public function bulkRegenerateAll(User $user)
    {
        $certificates = $user->certificates()->get();
        $successCount = 0;

        foreach ($certificates as $certificate) {
            try {
                $success = $this->certificateService->regenerateCertificateImage($certificate);
                if ($success) $successCount++;
            } catch (\Exception $e) {
                \Log::error('Failed to regenerate certificate ' . $certificate->id . ': ' . $e->getMessage());
            }
        }

        return redirect()->back()
            ->with('success', "Regenerated {$successCount} certificate images.");
    }

    public function bulkSendAll(User $user, Request $request)
    {
        $request->validate([
            'method' => 'required|in:email,notification,both',
        ]);

        $certificates = $user->certificates()->where('status', 'active')->get();
        $sentCount = 0;

        foreach ($certificates as $certificate) {
            try {
                if (in_array($request->method, ['email', 'both'])) {
                    $user->notify(new \App\Notifications\CertificateIssuedNotification($certificate));
                }

                if (in_array($request->method, ['notification', 'both'])) {
                    $user->notifications()->create([
                        'type' => 'certificate_issued',
                        'data' => [
                            'certificate_id' => $certificate->id,
                            'certificate_number' => $certificate->certificate_number,
                            'course_title' => $certificate->course->title,
                        ],
                        'read_at' => null,
                    ]);
                }

                $sentCount++;
            } catch (\Exception $e) {
                \Log::error('Failed to send certificate ' . $certificate->id . ': ' . $e->getMessage());
            }
        }

        return redirect()->back()
            ->with('success', "Sent {$sentCount} certificates to {$user->name}.");
    }

    public function bulkExportPdf(User $user)
    {
        $certificates = $user->certificates()->where('status', 'active')->get();

        if ($certificates->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No certificates found to export.');
        }

        $zip = new ZipArchive();
        $zipFileName = "certificates-{$user->id}-" . date('Y-m-d') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        if (!Storage::exists('temp')) {
            Storage::makeDirectory('temp');
        }

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($certificates as $certificate) {
                $pdfPath = $certificate->getPdfUrl();

                if ($pdfPath) {
                    $filePath = storage_path('app/public/' . str_replace('/storage/', '', $pdfPath));
                    if (file_exists($filePath)) {
                        $zip->addFile($filePath, "certificate-{$certificate->certificate_number}.pdf");
                    }
                }
            }

            $zip->close();
        }

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    public function bulkDownloadPdf(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:certificates,id',
        ]);

        $certificates = Certificate::whereIn('id', $request->ids)->get();

        $zip = new ZipArchive();
        $zipFileName = "certificates-" . date('Y-m-d-H-i-s') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        if (!Storage::exists('temp')) {
            Storage::makeDirectory('temp');
        }

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($certificates as $certificate) {
                $pdfPath = $certificate->getPdfUrl();

                if ($pdfPath) {
                    $filePath = storage_path('app/public/' . str_replace('/storage/', '', $pdfPath));
                    if (file_exists($filePath)) {
                        $zip->addFile($filePath, "certificate-{$certificate->certificate_number}.pdf");
                    }
                }
            }

            $zip->close();
        }

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    public function bulkDownloadImages(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:certificates,id',
        ]);

        $certificates = Certificate::whereIn('id', $request->ids)->get();

        $zip = new ZipArchive();
        $zipFileName = "certificate-images-" . date('Y-m-d-H-i-s') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        if (!Storage::exists('temp')) {
            Storage::makeDirectory('temp');
        }

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($certificates as $certificate) {
                $imagePath = $certificate->getShareableImageUrl();

                if ($imagePath) {
                    $filePath = storage_path('app/public/' . str_replace('/storage/', '', $imagePath));
                    if (file_exists($filePath)) {
                        $zip->addFile($filePath, "certificate-{$certificate->certificate_number}.png");
                    }
                }
            }

            $zip->close();
        }

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    public function export(User $user)
    {
        $certificates = $user->certificates()->get();

        $csvFileName = "certificates-{$user->id}-" . date('Y-m-d') . '.csv';
        $csvPath = storage_path('app/temp/' . $csvFileName);

        if (!Storage::exists('temp')) {
            Storage::makeDirectory('temp');
        }

        $file = fopen($csvPath, 'w');
        fputcsv($file, [
            'Certificate Number', 'Title', 'Course', 'Issue Date', 'Expiry Date',
            'Status', 'Downloads', 'Verification URL'
        ]);

        foreach ($certificates as $certificate) {
            fputcsv($file, [
                $certificate->certificate_number,
                $certificate->title,
                $certificate->course->title,
                $certificate->issue_date->format('Y-m-d'),
                $certificate->expiry_date?->format('Y-m-d') ?? 'N/A',
                $certificate->status,
                $certificate->download_count,
                $certificate->verification_url,
            ]);
        }

        fclose($file);

        return response()->download($csvPath, $csvFileName)->deleteFileAfterSend(true);
    }

    private function generateQrCode(Certificate $certificate): void
    {
        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
            ->size(300)
            ->generate($certificate->verification_url);

        $fileName = "certificates/qr_codes/{$certificate->certificate_number}.png";
        Storage::disk('public')->put($fileName, $qrCode);

        $certificate->update(['qr_code' => Storage::url($fileName)]);
    }

    private function generatePdf(Certificate $certificate): string
    {
        $data = $certificate->getCertificateData();

        $pdf = Pdf::loadView('certificates.pdf', [
            'certificate' => $data,
        ]);

        $fileName = "certificates/pdf/{$certificate->certificate_number}.pdf";
        $pdfPath = storage_path("app/public/{$fileName}");

        Storage::disk('public')->makeDirectory('certificates/pdf');
        $pdf->save($pdfPath);

        return Storage::url($fileName);
    }

    private function generateShareableImage(Certificate $certificate): string
    {
        return $this->certificateService->generateShareableImage($certificate);
    }

    private function prepareCertificateData(Certificate $certificate, Course $course, User $user, ?OrganizationProfile $organization): array
    {
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
        ];
    }

    private function deleteCertificateFiles(Certificate $certificate): void
    {
        try {
            // Delete QR code
            if ($certificate->qr_code) {
                $qrPath = str_replace('/storage/', '', $certificate->qr_code);
                Storage::disk('public')->delete($qrPath);
            }

            // Delete PDF
            if ($certificate->metadata && isset($certificate->metadata['pdf_path'])) {
                $pdfPath = str_replace('/storage/', '', $certificate->metadata['pdf_path']);
                Storage::disk('public')->delete($pdfPath);
            }

            // Delete image
            if ($certificate->metadata && isset($certificate->metadata['image_path'])) {
                $imagePath = str_replace('/storage/', '', $certificate->metadata['image_path']);
                Storage::disk('public')->delete($imagePath);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to delete certificate files: ' . $e->getMessage());
        }
    }

    public function togglePublic(Certificate $certificate)
    {
        $certificate->update([
            'is_public' => !$certificate->is_public,
        ]);

        return redirect()->back()
            ->with('success', 'Certificate public access updated.');
    }

    public function updateMetadata(Request $request, Certificate $certificate)
    {
        $request->validate([
            'metadata' => 'nullable|array',
        ]);

        $certificate->update([
            'metadata' => $request->metadata,
        ]);

        return redirect()->back()
            ->with('success', 'Certificate metadata updated.');
    }

    public function generateQr(Certificate $certificate)
    {
        // Generate QR code
        $this->generateQrCode($certificate);

        return redirect()->back()
            ->with('success', 'QR code generated successfully.');
    }
}
