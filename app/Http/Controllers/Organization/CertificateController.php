<?php
// app/Http\Controllers/Organization/CertificateController.php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\OrganizationProfile;
use App\Models\Course;
use App\Models\CertificateTemplate;
use App\Models\CourseEnrollment;
use App\Models\User;
use App\Services\CertificateGenerationService;
use App\Services\CertificateImageService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;

class CertificateController extends Controller
{
    protected $certificateService;
    protected $imageService;

    public function __construct(CertificateGenerationService $certificateService)
    {
        $this->certificateService = $certificateService;
        $this->imageService = new CertificateImageService();
    }

    public function index(Request $request)
    {
        $organization = auth()->user()->organizationProfile;

        if (!$organization) {
            return redirect()->route('organization.dashboard')
                ->with('error', 'Organization profile not found.');
        }

        $query = Certificate::where('organization_id', $organization->id)
            ->with(['user', 'course', 'organization'])
            ->latest();

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('certificate_number', 'like', "%{$request->search}%")
                  ->orWhere('title', 'like', "%{$request->search}%")
                  ->orWhereHas('user', function($q) use ($request) {
                      $q->where('name', 'like', "%{$request->search}%")
                        ->orWhere('email', 'like', "%{$request->search}%");
                  })
                  ->orWhereHas('course', function($q) use ($request) {
                      $q->where('title', 'like', "%{$request->search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'expired') {
                $query->where(function($q) {
                    $q->where('status', 'expired')
                      ->orWhere(function($q2) {
                          $q2->whereNotNull('expiry_date')
                             ->where('expiry_date', '<', now())
                             ->where('status', '!=', 'expired');
                      });
                });
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('issue_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('issue_date', '<=', $request->date_to);
        }

        $certificates = $query->paginate(20)->withQueryString();

        // Enhance certificates with additional data
        $certificates->getCollection()->transform(function ($certificate) {
            $certificate->image_url = $certificate->getShareableImageUrl();
            $certificate->pdf_url = $certificate->getPdfUrl();
            $certificate->is_expired = $certificate->isExpired();
            return $certificate;
        });

        // Calculate stats
        $stats = [
            'total' => Certificate::where('organization_id', $organization->id)->count(),
            'active' => Certificate::where('organization_id', $organization->id)
                ->where('status', 'active')
                ->count(),
            'expired' => Certificate::where('organization_id', $organization->id)
                ->where(function($q) {
                    $q->where('status', 'expired')
                      ->orWhere(function($q2) {
                          $q2->whereNotNull('expiry_date')
                             ->where('expiry_date', '<', now());
                      });
                })
                ->count(),
            'this_month' => Certificate::where('organization_id', $organization->id)
                ->whereMonth('issue_date', now()->month)
                ->whereYear('issue_date', now()->year)
                ->count(),
            'pending' => Certificate::where('organization_id', $organization->id)
                ->where('status', 'pending')
                ->count(),
            'total_downloads' => Certificate::where('organization_id', $organization->id)
                ->sum('download_count'),
        ];

        // Get organization courses
        $courses = $organization->courses()
            ->select('id', 'title', 'status')
            ->orderBy('title')
            ->get();

        // Get templates
        $templates = $organization->certificateTemplates()
            ->active()
            ->get();

        return Inertia::render('Organization/Certificates/Index', [
            'certificates' => $certificates,
            'stats' => $stats,
            'courses' => $courses,
            'templates' => $templates,
            'filters' => $request->only(['search', 'status', 'course_id', 'date_from', 'date_to']),
            'statuses' => ['active', 'pending', 'expired', 'revoked'],
        ]);
    }

    public function show(Certificate $certificate)
    {
        $organization = auth()->user()->organizationProfile;

        if ($certificate->organization_id !== $organization->id) {
            abort(403, 'Unauthorized');
        }

        $certificate->load(['user', 'course', 'organization']);

        $certificateData = $certificate->getCertificateData();
        $imageUrl = $certificate->getShareableImageUrl();
        $pdfUrl = $certificate->getPdfUrl();

        return Inertia::render('Organization/Certificates/Show', [
            'certificate' => array_merge(
                $certificate->toArray(),
                [
                    'image_url' => $imageUrl,
                    'pdf_url' => $pdfUrl,
                    'is_expired' => $certificate->isExpired(),
                    'is_expiring_soon' => $certificate->expiry_date &&
                        $certificate->expiry_date->isFuture() &&
                        $certificate->expiry_date->diffInDays(now()) <= 30,
                ]
            ),
            'certificate_data' => $certificateData,
            'can_regenerate' => $certificate->status === 'active',
        ]);
    }

    public function create(Request $request)
    {
        $organization = auth()->user()->organizationProfile;

        // Get students who have completed courses
        $students = $organization->students()
            ->with(['user' => function($query) {
                $query->select('id', 'name', 'email');
            }])
            ->get()
            ->pluck('user');

        // Get organization courses
        $courses = $organization->courses()
            ->where('status', 'published')
            ->select('id', 'title', 'subject')
            ->get();

        // Get templates
        $templates = $organization->certificateTemplates()
            ->active()
            ->get();

        return Inertia::render('Organization/Certificates/Create', [
            'students' => $students,
            'courses' => $courses,
            'templates' => $templates,
            'default_template' => $templates->where('is_default', true)->first(),
        ]);
    }

    public function store(Request $request)
    {
        $organization = auth()->user()->organizationProfile;

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'template_id' => 'nullable|exists:certificate_templates,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'send_notification' => 'boolean',
            'generate_files' => 'boolean',
        ]);

        $user = User::findOrFail($request->user_id);
        $course = Course::findOrFail($request->course_id);

        // Check if user is enrolled and completed the course
        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'completed')
            ->first();

        if (!$enrollment) {
            return redirect()->back()
                ->with('error', 'User has not completed this course.');
        }

        // Check if certificate already exists
        $existing = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return redirect()->back()
                ->with('error', 'Certificate already exists for this user and course.');
        }

        try {
            // Generate certificate using service
            $certificate = $this->certificateService->generateCertificate($user, $course, $organization);

            // Update with custom data if provided
            if ($request->filled('title') || $request->filled('description')) {
                $certificate->update([
                    'title' => $request->title,
                    'description' => $request->description,
                    'issue_date' => $request->issue_date,
                    'expiry_date' => $request->expiry_date,
                ]);
            }

            // Send notification if requested
            if ($request->send_notification) {
                $user->notify(new \App\Notifications\CertificateIssuedNotification($certificate));
            }

            return redirect()->route('organization.certificates.show', $certificate->id)
                ->with('success', 'Certificate generated successfully!');

        } catch (\Exception $e) {
            \Log::error('Failed to generate certificate: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to generate certificate: ' . $e->getMessage());
        }
    }

    public function batchGenerate(Request $request)
    {
        $organization = auth()->user()->organizationProfile;

        $request->validate([
            'course_ids' => 'required|array|min:1',
            'course_ids.*' => 'exists:courses,id',
            'send_notifications' => 'boolean',
            'generate_files' => 'boolean',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        $results = $this->certificateService->batchGenerateForOrganization(
            $organization,
            $request->course_ids
        );

        // Process notifications if requested
        if ($request->send_notifications) {
            foreach ($results['successful'] as $success) {
                if (isset($success['certificate_id'])) {
                    $certificate = Certificate::find($success['certificate_id']);
                    if ($certificate && $certificate->user) {
                        $certificate->user->notify(new \App\Notifications\CertificateIssuedNotification($certificate));
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'results' => $results,
            'message' => sprintf(
                'Batch generation completed: %d successful, %d failed.',
                count($results['successful']),
                count($results['failed'])
            ),
        ]);
    }

    public function update(Request $request, Certificate $certificate)
    {
        $organization = auth()->user()->organizationProfile;

        if ($certificate->organization_id !== $organization->id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'status' => 'required|in:active,pending,expired,revoked',
            'is_public' => 'boolean',
        ]);

        $certificate->update([
            'title' => $request->title,
            'description' => $request->description,
            'issue_date' => $request->issue_date,
            'expiry_date' => $request->expiry_date,
            'status' => $request->status,
            'is_public' => $request->is_public ?? $certificate->is_public,
        ]);

        // Log update in metadata
        $metadata = $certificate->metadata ?? [];
        if (!isset($metadata['updates'])) {
            $metadata['updates'] = [];
        }

        $metadata['updates'][] = [
            'updated_at' => now()->toISOString(),
            'updated_by' => auth()->id(),
            'changes' => $request->only(['title', 'description', 'issue_date', 'expiry_date', 'status', 'is_public']),
        ];

        $certificate->update(['metadata' => $metadata]);

        return redirect()->back()
            ->with('success', 'Certificate updated successfully.');
    }

    public function revoke(Request $request, Certificate $certificate)
    {
        $organization = auth()->user()->organizationProfile;

        if ($certificate->organization_id !== $organization->id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $oldStatus = $certificate->status;

        $certificate->update([
            'status' => 'revoked',
        ]);

        // Update metadata
        $metadata = $certificate->metadata ?? [];
        if (!isset($metadata['revocations'])) {
            $metadata['revocations'] = [];
        }

        $metadata['revocations'][] = [
            'revoked_at' => now()->toISOString(),
            'revoked_by' => auth()->id(),
            'reason' => $request->reason,
            'previous_status' => $oldStatus,
        ];

        $certificate->update(['metadata' => $metadata]);

        // Notify user
        if ($certificate->user) {
            $certificate->user->notify(new \App\Notifications\CertificateRevokedNotification($certificate, $request->reason));
        }

        return redirect()->back()
            ->with('success', 'Certificate has been revoked.');
    }

    public function renew(Request $request, Certificate $certificate)
    {
        $organization = auth()->user()->organizationProfile;

        if ($certificate->organization_id !== $organization->id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'expiry_date' => 'required|date|after:today',
            'reason' => 'nullable|string|max:500',
            'regenerate_files' => 'boolean',
            'notify_user' => 'boolean',
        ]);

        $oldExpiry = $certificate->expiry_date;

        $certificate->update([
            'expiry_date' => $request->expiry_date,
            'status' => 'active',
        ]);

        // Regenerate files if requested
        if ($request->regenerate_files) {
            try {
                $this->certificateService->regenerateCertificateImage($certificate);
            } catch (\Exception $e) {
                \Log::error('Failed to regenerate certificate files: ' . $e->getMessage());
            }
        }

        // Update metadata
        $metadata = $certificate->metadata ?? [];
        if (!isset($metadata['renewals'])) {
            $metadata['renewals'] = [];
        }

        $metadata['renewals'][] = [
            'renewed_at' => now()->toISOString(),
            'renewed_by' => auth()->id(),
            'reason' => $request->reason,
            'old_expiry' => $oldExpiry?->toISOString(),
            'new_expiry' => $request->expiry_date,
        ];

        $certificate->update(['metadata' => $metadata]);

        // Notify user if requested
        if ($request->notify_user && $certificate->user) {
            $certificate->user->notify(new \App\Notifications\CertificateRenewedNotification($certificate));
        }

        return redirect()->back()
            ->with('success', 'Certificate renewed successfully.');
    }

    public function regenerateImage(Certificate $certificate)
    {
        $organization = auth()->user()->organizationProfile;

        if ($certificate->organization_id !== $organization->id) {
            abort(403, 'Unauthorized');
        }

        try {
            $success = $this->certificateService->regenerateCertificateImage($certificate);

            if ($success) {
                return redirect()->back()
                    ->with('success', 'Certificate image regenerated successfully.');
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
        $organization = auth()->user()->organizationProfile;

        if ($certificate->organization_id !== $organization->id) {
            abort(403, 'Unauthorized');
        }

        if (!$certificate->canDownload()) {
            return redirect()->back()
                ->with('error', 'Certificate cannot be downloaded.');
        }

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
                    ->with('error', 'Failed to generate PDF.');
            }
        }

        // Increment download count
        $certificate->increment('download_count');

        $fileName = "certificate-{$certificate->certificate_number}.pdf";
        $filePath = storage_path('app/public/' . str_replace('/storage/', '', $pdfPath));

        return response()->download($filePath, $fileName);
    }

    public function downloadImage(Certificate $certificate)
    {
        $organization = auth()->user()->organizationProfile;

        if ($certificate->organization_id !== $organization->id) {
            abort(403, 'Unauthorized');
        }

        $imagePath = $certificate->getShareableImageUrl();

        if (empty($imagePath)) {
            try {
                $imagePath = $this->certificateService->generateShareableImage($certificate);

                $metadata = $certificate->metadata ?? [];
                $metadata['image_path'] = $imagePath;
                $certificate->update(['metadata' => $metadata]);
            } catch (\Exception $e) {
                \Log::error('Failed to generate image: ' . $e->getMessage());
                return redirect()->back()
                    ->with('error', 'Failed to generate image.');
            }
        }

        $fileName = "certificate-{$certificate->certificate_number}.png";
        $filePath = storage_path('app/public/' . str_replace('/storage/', '', $imagePath));

        return response()->download($filePath, $fileName);
    }

    public function bulkDownload(Request $request)
    {
        $organization = auth()->user()->organizationProfile;

        $request->validate([
            'certificate_ids' => 'required|array|min:1',
            'certificate_ids.*' => 'exists:certificates,id',
            'format' => 'required|in:pdf,image,zip',
        ]);

        // Verify all certificates belong to organization
        $certificates = Certificate::whereIn('id', $request->certificate_ids)
            ->where('organization_id', $organization->id)
            ->get();

        if ($certificates->count() !== count($request->certificate_ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Some certificates are not authorized.',
            ], 403);
        }

        if ($request->format === 'zip') {
            return $this->downloadAsZip($certificates);
        }

        // For single format downloads
        $files = [];
        foreach ($certificates as $certificate) {
            if ($request->format === 'pdf') {
                $path = $certificate->getPdfUrl();
                $type = 'pdf';
            } else {
                $path = $certificate->getShareableImageUrl();
                $type = 'image';
            }

            if ($path) {
                $filePath = storage_path('app/public/' . str_replace('/storage/', '', $path));
                if (file_exists($filePath)) {
                    $files[] = [
                        'path' => $filePath,
                        'name' => "certificate-{$certificate->certificate_number}.{$type}",
                    ];
                }
            }
        }

        if (empty($files)) {
            return response()->json([
                'success' => false,
                'message' => 'No files found for download.',
            ], 404);
        }

        // If only one file, return it directly
        if (count($files) === 1) {
            $file = $files[0];
            return response()->download($file['path'], $file['name']);
        }

        // Multiple files - create ZIP
        return $this->createZipFromFiles($files, "certificates-{$organization->id}-" . date('Y-m-d') . ".zip");
    }

    public function export(Request $request)
    {
        $organization = auth()->user()->organizationProfile;

        $request->validate([
            'format' => 'required|in:csv,excel',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:active,pending,expired,revoked',
        ]);

        $query = Certificate::where('organization_id', $organization->id)
            ->with(['user:id,name,email', 'course:id,title']);

        if ($request->filled('start_date')) {
            $query->whereDate('issue_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('issue_date', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $certificates = $query->get();

        if ($request->format === 'csv') {
            return $this->exportAsCsv($certificates);
        }

        return $this->exportAsExcel($certificates);
    }

    public function settings()
    {
        $organization = auth()->user()->organizationProfile;

        $templates = $organization->certificateTemplates()
            ->orderBy('is_default', 'desc')
            ->orderBy('is_active', 'desc')
            ->get();

        $defaultTemplate = $templates->where('is_default', true)->first();

        $settings = $organization->settings['certificates'] ?? [
            'auto_generate' => false,
            'expiry_years' => 2,
            'include_logo' => true,
            'default_template_id' => $defaultTemplate?->id,
            'signature_image' => null,
        ];

        return Inertia::render('Organization/Certificates/Settings', [
            'organization' => $organization,
            'templates' => $templates,
            'settings' => $settings,
            'signature_image_url' => isset($settings['signature_image'])
                ? Storage::url($settings['signature_image'])
                : null,
        ]);
    }

    public function updateSettings(Request $request)
    {
        $organization = auth()->user()->organizationProfile;

        $request->validate([
            'default_template_id' => 'nullable|exists:certificate_templates,id',
            'auto_generate_certificates' => 'boolean',
            'certificate_expiry_years' => 'integer|min:1|max:10',
            'include_organization_logo' => 'boolean',
            'signature_image' => 'nullable|image|max:5120', // 5MB
            'remove_signature' => 'boolean',
            'notification_settings' => 'nullable|array',
            'custom_message' => 'nullable|string|max:1000',
        ]);

        $settings = $organization->settings ?? [];
        $certSettings = $settings['certificates'] ?? [];

        // Update certificate settings
        $certSettings = array_merge($certSettings, [
            'auto_generate' => $request->boolean('auto_generate_certificates', $certSettings['auto_generate'] ?? false),
            'expiry_years' => $request->certificate_expiry_years ?? ($certSettings['expiry_years'] ?? 2),
            'include_logo' => $request->boolean('include_organization_logo', $certSettings['include_logo'] ?? true),
            'default_template_id' => $request->default_template_id ?? ($certSettings['default_template_id'] ?? null),
            'notification_settings' => $request->notification_settings ?? ($certSettings['notification_settings'] ?? []),
            'custom_message' => $request->custom_message ?? ($certSettings['custom_message'] ?? null),
        ]);

        // Handle signature image
        if ($request->boolean('remove_signature') && isset($certSettings['signature_image'])) {
            Storage::disk('public')->delete($certSettings['signature_image']);
            $certSettings['signature_image'] = null;
        } elseif ($request->hasFile('signature_image')) {
            // Delete old signature if exists
            if (isset($certSettings['signature_image'])) {
                Storage::disk('public')->delete($certSettings['signature_image']);
            }

            $path = $request->file('signature_image')->store("organizations/{$organization->id}/signatures", 'public');
            $certSettings['signature_image'] = $path;
        }

        $settings['certificates'] = $certSettings;
        $organization->update(['settings' => $settings]);

        return redirect()->back()
            ->with('success', 'Certificate settings updated successfully.');
    }

    public function previewTemplate(CertificateTemplate $template)
    {
        $organization = auth()->user()->organizationProfile;

        if ($template->organization_id !== $organization->id) {
            abort(403, 'Unauthorized');
        }

        try {
            $previewUrl = $this->certificateService->generateTemplatePreview($template);

            return response()->json([
                'success' => true,
                'preview_url' => $previewUrl,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to generate template preview: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate preview.',
            ], 500);
        }
    }

    private function downloadAsZip($certificates)
    {
        $zip = new ZipArchive();
        $zipFileName = storage_path("app/public/temp/certificates-" . now()->timestamp . ".zip");

        // Ensure directory exists
        Storage::disk('public')->makeDirectory('temp');

        if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($certificates as $certificate) {
                $pdfPath = $certificate->getPdfUrl();
                $imagePath = $certificate->getShareableImageUrl();

                if ($pdfPath) {
                    $filePath = storage_path('app/public/' . str_replace('/storage/', '', $pdfPath));
                    if (file_exists($filePath)) {
                        $zip->addFile($filePath, "pdf/{$certificate->certificate_number}.pdf");
                    }
                }

                if ($imagePath) {
                    $filePath = storage_path('app/public/' . str_replace('/storage/', '', $imagePath));
                    if (file_exists($filePath)) {
                        $zip->addFile($filePath, "images/{$certificate->certificate_number}.png");
                    }
                }
            }

            $zip->close();
        }

        return response()->download($zipFileName)->deleteFileAfterSend(true);
    }

    private function createZipFromFiles($files, $zipName)
    {
        $zip = new ZipArchive();
        $zipPath = storage_path("app/public/temp/{$zipName}");

        Storage::disk('public')->makeDirectory('temp');

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($files as $file) {
                $zip->addFile($file['path'], $file['name']);
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    private function exportAsCsv($certificates)
    {
        $fileName = "certificates-export-" . date('Y-m-d-H-i-s') . ".csv";
        $filePath = storage_path("app/public/temp/{$fileName}");

        Storage::disk('public')->makeDirectory('temp');

        $file = fopen($filePath, 'w');

        // CSV headers
        fputcsv($file, [
            'Certificate Number',
            'Student Name',
            'Student Email',
            'Course Title',
            'Issue Date',
            'Expiry Date',
            'Status',
            'Downloads',
            'Verification URL',
            'Public Access',
        ]);

        // Data rows
        foreach ($certificates as $certificate) {
            fputcsv($file, [
                $certificate->certificate_number,
                $certificate->user->name,
                $certificate->user->email,
                $certificate->course->title,
                $certificate->issue_date->format('Y-m-d'),
                $certificate->expiry_date?->format('Y-m-d') ?? 'N/A',
                $certificate->status,
                $certificate->download_count,
                $certificate->verification_url,
                $certificate->is_public ? 'Yes' : 'No',
            ]);
        }

        fclose($file);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    private function exportAsExcel($certificates)
    {
        // This would require Laravel Excel package
        // For now, return CSV as fallback
        return $this->exportAsCsv($certificates);
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
}
