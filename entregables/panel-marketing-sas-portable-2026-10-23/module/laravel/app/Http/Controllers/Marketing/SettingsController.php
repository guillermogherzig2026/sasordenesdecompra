<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\Company;
use App\Models\Marketing\OfficeHour;
use App\Support\MarketingWorkspace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(Request $request, MarketingWorkspace $workspace): View
    {
        $company = $workspace->selectedCompany($request);
        $campaign = $workspace->selectedCampaign($request, $company);

        if (! $company) {
            return view('marketing.leadflow.empty', [
                'heading' => 'Configuracion de marketing',
                'message' => 'Registra una empresa de marketing para configurar sus integraciones.',
            ]);
        }

        $company->load(['whatsappNumbers', 'tags', 'automationRules', 'campaigns']);
        $officeHours = OfficeHour::query()->where('company_id', $company->id)->orderBy('day_of_week')->get();

        return view('marketing.leadflow.settings.index', compact('company', 'campaign', 'officeHours'));
    }

    public function updateCompany(Request $request): RedirectResponse
    {
        $company = $this->companyFromRequest($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'domain' => ['nullable', 'string', 'max:160'],
            'industry' => ['nullable', 'string', 'max:120'],
            'brand_color' => ['nullable', 'string', 'max:20'],
        ]);

        $company->update($data);

        return back()->with('status', 'Datos de empresa actualizados.');
    }

    public function storeWhatsAppNumber(Request $request): RedirectResponse
    {
        $company = $this->companyFromRequest($request);

        $data = $request->validate([
            'display_name' => ['nullable', 'string', 'max:120'],
            'phone_number' => ['required', 'string', 'max:40'],
            'provider_phone_id' => ['nullable', 'string', 'max:160'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $company->whatsappNumbers()->create($data);

        return back()->with('status', 'Numero de WhatsApp agregado.');
    }

    public function storeTag(Request $request): RedirectResponse
    {
        $company = $this->companyFromRequest($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);

        $company->tags()->firstOrCreate(['name' => $data['name']], ['color' => $data['color'] ?? '#22c55e']);

        return back()->with('status', 'Etiqueta guardada.');
    }

    public function storeAutomation(Request $request): RedirectResponse
    {
        $company = $this->companyFromRequest($request);

        $data = $request->validate([
            'campaign_id' => ['nullable', 'exists:marketing_campaigns,id'],
            'name' => ['required', 'string', 'max:160'],
            'trigger' => ['required', 'string', 'max:120'],
            'action' => ['required', 'string', 'max:120'],
            'active' => ['nullable', 'boolean'],
        ]);

        $data['active'] = $request->boolean('active');
        $data['payload'] = ['message' => $request->input('payload_message')];
        $company->automationRules()->create($data);

        return back()->with('status', 'Automatizacion agregada.');
    }

    public function updateOfficeHours(Request $request): RedirectResponse
    {
        $company = $this->companyFromRequest($request);

        foreach (range(1, 7) as $day) {
            OfficeHour::query()->updateOrCreate(
                ['company_id' => $company->id, 'campaign_id' => null, 'day_of_week' => $day],
                [
                    'opens_at' => $request->input("hours.$day.opens_at"),
                    'closes_at' => $request->input("hours.$day.closes_at"),
                    'enabled' => $request->boolean("hours.$day.enabled"),
                ]
            );
        }

        return back()->with('status', 'Horarios actualizados.');
    }

    private function companyFromRequest(Request $request): Company
    {
        $companyId = $request->input('company_id') ?: $request->session()->get('marketing_company_id');

        return Company::query()->findOrFail($companyId);
    }
}
