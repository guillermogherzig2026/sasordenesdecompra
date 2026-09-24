<?php

namespace App\Http\Controllers;

use App\Models\SecurityIncident;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class SecurityIncidentController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $companyId = $request->integer('security_company_id');
        $branchId = $request->integer('security_branch_id');
        $validated = $request->validateWithBag('incident', [
            'security_company_id' => ['required', 'integer', 'exists:security_companies,id'],
            'security_branch_id' => [
                'nullable', 'integer', 'required_with:security_camera_id',
                Rule::exists('security_branches', 'id')->where('security_company_id', $companyId),
            ],
            'security_camera_id' => [
                'nullable', 'integer',
                Rule::exists('security_cameras', 'id')->where('security_branch_id', $branchId),
            ],
            'title' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:3000'],
            'priority' => ['required', Rule::in(['critical', 'medium', 'low'])],
            'occurred_at' => ['required', 'date'],
        ]);

        $validated['security_branch_id'] = $validated['security_branch_id'] ?? null;
        $validated['security_camera_id'] = $validated['security_camera_id'] ?? null;
        $validated['created_by'] = $request->user()->id;
        $validated['status'] = 'pending';
        SecurityIncident::create($validated);

        $incidentDate = Carbon::parse($validated['occurred_at'])->toDateString();

        return redirect()->route('security.index', [
            'company' => $validated['security_company_id'],
            'from_date' => $incidentDate,
            'to_date' => $incidentDate,
        ])->with('status', 'Incidencia registrada.');
    }

    public function updateStatus(Request $request, SecurityIncident $securityIncident): RedirectResponse
    {
        $validated = $request->validate(['status' => ['required', Rule::in(['pending', 'resolved'])]]);
        $securityIncident->update([
            'status' => $validated['status'],
            'resolved_at' => $validated['status'] === 'resolved' ? now() : null,
        ]);

        return back()->with('status', $validated['status'] === 'resolved'
            ? 'Incidencia resuelta.'
            : 'Incidencia reabierta.');
    }
}
