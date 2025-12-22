<?php
// app/Http/Controllers/Organization/CertificateController.php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\OrganizationProfile;
use App\Models\Course;
use App\Services\CertificateGenerationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CertificateController extends Controller
{
    protected $certificateService;

    public function __construct(CertificateGenerationService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    public function index(Request $request)
    {
        $organization = auth()->user()->organizationProfile;
        
        $certificates = Certificate::where('organization_id', $organization->id)
            ->with(['user', 'course'])
            ->orderBy('issue_date', 'desc')
            ->paginate(20);

        $stats = [
            'total' => Certificate::where('organization_id', $organization->id)->count(),
            'active' => Certificate::where('organization_id', $organization->id)->active()->count(),
            'expired' => Certificate::where('organization_id', $organization->id)->expired()->count(),
            'this_month' => Certificate::where('organization_id', $organization->id)
                ->whereMonth('issue_date', now()->month)
                ->count(),
        ];

        return Inertia::render('Organization/Certificates/Index', [
            'certificates' => $certificates,
            'stats' => $stats,
            'courses' => $organization->courses()->where('status', 'completed')->get(),
        ]);
    }

    public function batchGenerate(Request $request)
    {
        $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        $organization = auth()->user()->organizationProfile;
        
        $results = $this->certificateService->batchGenerateForOrganization(
            $organization,
            $request->course_ids
        );

        return response()->json([
            'success' => true,
            'results' => $results,
            'message' => 'Batch certificate generation completed.',
        ]);
    }

    public function revoke(Certificate $certificate)
    {
        $organization = auth()->user()->organizationProfile;
        
        if ($certificate->organization_id !== $organization->id) {
            abort(403, 'Unauthorized');
        }

        $certificate->update([
            'status' => 'revoked',
            'metadata' => array_merge($certificate->metadata ?? [], [
                'revoked_at' => now(),
                'revoked_by' => auth()->id(),
                'revocation_reason' => $request->reason ?? 'Revoked by organization',
            ]),
        ]);

        return redirect()->back()
            ->with('success', 'Certificate has been revoked.');
    }

    public function settings()
    {
        $organization = auth()->user()->organizationProfile;
        $templates = $organization->certificateTemplates()->get();
        
        return Inertia::render('Organization/Certificates/Settings', [
            'organization' => $organization,
            'templates' => $templates,
            'default_template' => $templates->where('is_default', true)->first(),
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
            'signature_image' => 'nullable|image|max:2048',
        ]);

        $settings = $organization->settings ?? [];
        $settings['certificates'] = [
            'auto_generate' => $request->auto_generate_certificates ?? false,
            'expiry_years' => $request->certificate_expiry_years ?? 2,
            'include_logo' => $request->include_organization_logo ?? true,
            'default_template_id' => $request->default_template_id,
        ];

        if ($request->hasFile('signature_image')) {
            $path = $request->file('signature_image')->store("organizations/{$organization->id}/signatures", 'public');
            $settings['certificates']['signature_image'] = $path;
        }

        $organization->update(['settings' => $settings]);

        return redirect()->back()
            ->with('success', 'Certificate settings updated successfully.');
    }
}