<?php
// app/Http/Controllers/Student/CertificateController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Services\CertificateGenerationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

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
        
        $certificates = $user->certificates()
            ->with(['course', 'organization'])
            ->orderBy('issue_date', 'desc')
            ->paginate(12);

        return Inertia::render('Student/Certificates/Index', [
            'certificates' => $certificates,
            'total_certificates' => $user->certificates()->count(),
            'active_certificates' => $user->certificates()->active()->count(),
        ]);
    }

    public function show(Certificate $certificate)
    {
        // Authorization - ensure user owns the certificate
        if ($certificate->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $certificate->load(['course', 'organization']);
        
        $certificateData = $certificate->getCertificateData();
        $shareableImage = $this->certificateService->generateShareableImage($certificate);

        return Inertia::render('Student/Certificates/Show', [
            'certificate' => $certificate,
            'certificate_data' => $certificateData,
            'shareable_image' => $shareableImage,
            'can_download' => $certificate->canDownload(),
        ]);
    }

    public function download(Certificate $certificate)
    {
        // Authorization
        if ($certificate->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if (!$certificate->canDownload()) {
            abort(403, 'Certificate is not available for download');
        }

        // Increment download count
        $certificate->incrementDownloadCount();

        // Get PDF path from metadata
        $pdfPath = $certificate->metadata['pdf_path'] ?? null;
        
        if ($pdfPath && Storage::disk('public')->exists(str_replace('/storage/', '', $pdfPath))) {
            return response()->download(
                storage_path('app/public/' . str_replace('/storage/', '', $pdfPath)),
                "Certificate-{$certificate->certificate_number}.pdf"
            );
        }

        // Generate PDF on the fly if not exists
        $data = $certificate->getCertificateData();
        $pdf = Pdf::loadView('certificates.pdf', [
            'certificate' => $data,
            'template' => $certificate->organization?->certificateTemplate,
        ]);

        return $pdf->download("Certificate-{$certificate->certificate_number}.pdf");
    }

    public function share(Certificate $certificate)
    {
        // Generate shareable image
        $imageUrl = $this->certificateService->generateShareableImage($certificate);
        
        $shareLinks = [
            'linkedin' => "https://www.linkedin.com/profile/add?startTask=CERTIFICATION_NAME&name={$certificate->course->title}&organizationName=" . urlencode($certificate->organization?->name ?? 'Olilearn') . "&issueYear={$certificate->issue_date->year}&certUrl=" . urlencode($certificate->verification_url),
            'twitter' => "https://twitter.com/intent/tweet?text=" . urlencode("I just completed '{$certificate->course->title}' on Olilearn! Check out my certificate: {$certificate->verification_url}"),
            'facebook' => "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($certificate->verification_url),
        ];

        return response()->json([
            'image_url' => $imageUrl,
            'verification_url' => $certificate->verification_url,
            'share_links' => $shareLinks,
            'certificate_number' => $certificate->certificate_number,
        ]);
    }

    public function verify($hash)
    {
        $certificate = Certificate::whereRaw("SHA2(CONCAT(certificate_number, user_id, course_id), 256) = ?", [$hash])
            ->with(['user', 'course', 'organization'])
            ->firstOrFail();

        $certificateData = $certificate->getCertificateData();

        return Inertia::render('Public/CertificateVerification', [
            'certificate' => $certificateData,
            'is_valid' => $certificate->canDownload(),
            'verification_date' => now()->format('F j, Y, g:i a'),
        ]);
    }

    public function requestCertificate(Course $course)
    {
        $user = auth()->user();
        
        // Check if already has certificate
        $existing = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            return redirect()->route('student.certificates.show', $existing->id)
                ->with('info', 'Certificate already exists.');
        }

        // Check eligibility
        if (!$this->certificateService->isEligibleForCertificate($user, $course)) {
            return redirect()->route('student.courses.show', $course->id)
                ->with('error', 'You are not eligible for a certificate yet. Please complete all requirements.');
        }

        // Generate certificate
        try {
            $certificate = $this->certificateService->generateCertificate($user, $course);
            
            return redirect()->route('student.certificates.show', $certificate->id)
                ->with('success', 'Certificate generated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to generate certificate: ' . $e->getMessage());
        }
    }
}