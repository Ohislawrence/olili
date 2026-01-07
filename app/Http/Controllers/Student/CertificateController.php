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
                default:
                    $query->latest();
            }
        }

        $certificates = $query->paginate(12);

        // Calculate stats
        $stats = [
            'total_certificates' => $user->certificates()->count(),
            'active_certificates' => $user->certificates()->where('status', 'active')->count(),
            'expired_certificates' => $user->certificates()->where('status', 'expired')->count(),
            'total_downloads' => $user->certificates()->sum('download_count'),
        ];

        return Inertia::render('Student/Certificates/Index', [
            'certificates' => $certificates,
            'stats' => $stats,
            'filters' => $filters,
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

        // Increment download count
        $certificate->increment('download_count');

        // Get certificate data
        $certificateData = $certificate->getCertificateData();

        // Generate PDF
        $pdf = Pdf::loadView('certificates.pdf', [
            'certificate' => $certificateData,
            'user' => auth()->user(),
        ]);

        return $pdf->download("certificate-{$certificate->certificate_number}.pdf");
    }

    public function share(Certificate $certificate, Request $request)
    {
        // Authorization
        if ($certificate->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'platform' => 'required|in:linkedin,twitter,facebook,link',
        ]);

        $urls = [
            'linkedin' => "https://www.linkedin.com/profile/add?startTask=CERTIFICATION_NAME&name=" . urlencode($certificate->course->title) . "&organizationName=" . urlencode($certificate->organization?->name ?? 'Olilearn') . "&issueYear=" . date('Y', strtotime($certificate->issue_date)) . "&certUrl=" . urlencode($certificate->verification_url),
            'twitter' => "https://twitter.com/intent/tweet?text=" . urlencode("I just completed '{$certificate->course->title}' on Olilearn! Check out my certificate: ") . "&url=" . urlencode($certificate->verification_url),
            'facebook' => "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($certificate->verification_url),
            'link' => $certificate->verification_url,
        ];

        if ($request->platform === 'link') {
            // Copy to clipboard
            return response()->json([
                'url' => $urls['link'],
                'message' => 'Link copied to clipboard',
            ]);
        }

        return response()->json([
            'url' => $urls[$request->platform],
            'message' => 'Share URL generated',
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

    public function request()
    {
        $user = auth()->user();

        // Get completed courses
        $completedCourses = $user->enrolledCourses()
            ->where('status', 'completed')
            ->with(['modules', 'capstoneProject'])
            ->get();

        // Get existing certificates
        $certificates = $user->certificates()->get(['id', 'course_id']);

        return Inertia::render('Student/Certificates/Request', [
            'completedCourses' => $completedCourses,
            'certificates' => $certificates,
        ]);
    }

    public function requestCertificate(Course $course)
    {
        $user = auth()->user();

        // Check if user owns the course
        if ($course->student_profile_id !== $user->studentProfile->id) {
            abort(403, 'Unauthorized');
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

        // Check eligibility
        if (!$course->isCompleted()) {
            return redirect()->back()
                ->with('error', 'Course is not completed yet.');
        }

        if ($course->progress_percentage < 70) {
            return redirect()->back()
                ->with('error', 'Minimum 70% score required for certificate.');
        }

        if ($course->capstoneProject && !$course->capstoneProject->is_approved) {
            return redirect()->back()
                ->with('error', 'Capstone project must be approved.');
        }

        // Generate certificate
        try {
            $certificate = Certificate::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'title' => "Certificate of Completion - {$course->title}",
                'description' => "This certifies that {$user->name} has successfully completed the course '{$course->title}'.",
                'issue_date' => now(),
                'expiry_date' => now()->addYears(2),
                'status' => 'active',
                'is_public' => true,
            ]);

            // Generate certificate number
            $prefix = 'OLCERT';
            $year = date('Y');
            $sequence = str_pad(Certificate::whereYear('issue_date', $year)->count() + 1, 6, '0', STR_PAD_LEFT);
            $certificate->certificate_number = "{$prefix}-{$year}-{$sequence}";

            // Generate verification URL
            $hash = hash('sha256', $certificate->certificate_number . $user->id . $course->id . config('app.key'));
            $certificate->verification_url = route('certificates.verify', $hash);

            $certificate->save();

            return redirect()->route('student.certificates.show', $certificate->id)
                ->with('success', 'Certificate generated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to generate certificate: ' . $e->getMessage());
        }
    }

    public function exportAll()
    {
        $user = auth()->user();
        $certificates = $user->certificates()->with('course')->get();

        // Create ZIP file
        $zip = new \ZipArchive();
        $zipFileName = storage_path("app/certificates-{$user->id}-" . now()->timestamp . ".zip");

        if ($zip->open($zipFileName, \ZipArchive::CREATE) === TRUE) {
            foreach ($certificates as $certificate) {
                // Generate PDF for each certificate
                $pdf = Pdf::loadView('certificates.pdf', [
                    'certificate' => $certificate->getCertificateData(),
                    'user' => $user,
                ]);

                $pdfContent = $pdf->output();
                $zip->addFromString("{$certificate->certificate_number}.pdf", $pdfContent);
            }
            $zip->close();
        }

        return response()->download($zipFileName)->deleteFileAfterSend(true);
    }
}
