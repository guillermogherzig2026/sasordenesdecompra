@extends('marketing.leadflow.layout', ['title' => 'Configuración | LeadFlow'])

@section('content')
<div class="page-head">
    <div>
        <h1>Configuración</h1>
        <p>Configura datos generales, integraciones y preferencias del equipo.</p>
    </div>
</div>

<div class="selector-row" style="margin-bottom:16px">
    <div class="selector">
        <div style="display:flex;gap:12px;align-items:center">
            <span class="logo-circle small" style="background:{{ $company->brand_color }}">{{ $company->logo_initials }}</span>
            <div><strong>{{ $company->name }}</strong><br><span class="status-dot active">Activa</span></div>
        </div>
        <span>⌄</span>
    </div>
    <div class="selector">
        <div style="display:flex;gap:12px;align-items:center">
            <span class="metric-icon" style="width:34px;height:34px;font-size:15px">◁</span>
            <div><strong>{{ $campaign?->name ?? 'Todas las campañas' }}</strong><br><span class="muted small">Campaña opcional</span></div>
        </div>
        <span>⌄</span>
    </div>
</div>

<div class="split">
    <div class="grid">
        <details class="panel pad" open>
            <summary style="cursor:pointer;display:flex;align-items:center;justify-content:space-between">
                <span style="display:flex;align-items:center;gap:16px"><span class="metric-icon">▦</span><strong>Empresa seleccionada</strong></span>
                <span>Editar</span>
            </summary>
            <form method="post" action="{{ route('marketing.settings.company.update') }}" class="form-grid" style="margin-top:18px">
                @csrf @method('patch')
                <input type="hidden" name="company_id" value="{{ $company->id }}">
                <div class="field">
                    <label>Nombre</label>
                    <input class="input" name="name" value="{{ old('name', $company->name) }}" required>
                </div>
                <div class="field">
                    <label>Dominio</label>
                    <input class="input" name="domain" value="{{ old('domain', $company->domain) }}">
                </div>
                <div class="field">
                    <label>Industria</label>
                    <input class="input" name="industry" value="{{ old('industry', $company->industry) }}">
                </div>
                <div class="field">
                    <label>Color de marca</label>
                    <input class="input" type="color" name="brand_color" value="{{ old('brand_color', $company->brand_color) }}">
                </div>
                <div class="field full"><button class="btn primary" type="submit">Guardar datos</button></div>
            </form>
        </details>

        <details class="panel pad" open>
            <summary style="cursor:pointer;display:flex;align-items:center;justify-content:space-between">
                <span style="display:flex;align-items:center;gap:16px"><span class="metric-icon">☏</span><strong>Números de WhatsApp</strong></span>
                <span class="badge green">{{ $company->whatsappNumbers->whereNotNull('meta_phone_number_id')->count() }} Cloud API configurado</span>
            </summary>
            <p class="muted small" style="margin-top:16px">La conexión oficial se configura en <a href="{{ route('marketing.conversations.index', ['company_id' => $company->id]) }}">Conversaciones</a>. Los números sin configuración Cloud API son registros internos y no envían mensajes por Meta.</p>
            <form method="post" action="{{ route('marketing.settings.whatsapp.store') }}" class="form-grid" style="margin-top:18px">
                @csrf
                <input type="hidden" name="company_id" value="{{ $company->id }}">
                <div class="field">
                    <label>Nombre visible</label>
                    <input class="input" name="display_name" placeholder="Ventas">
                </div>
                <div class="field">
                    <label>Número</label>
                    <input class="input" name="phone_number" placeholder="+52 55 1234 5678" required>
                </div>
                <div class="field">
                    <label>ID externo (referencia)</label>
                    <input class="input" name="provider_phone_id" placeholder="phone_number_id">
                </div>
                <div class="field">
                    <label>Estado</label>
                    <select class="select" name="status"><option value="active">Activo</option><option value="inactive">Inactivo</option></select>
                </div>
                <div class="field full"><button class="btn" type="submit">Agregar número</button></div>
            </form>
        </details>

        <details class="panel pad" id="marketing-automations" @if(request('section') === 'automations') open @endif>
            <summary style="cursor:pointer;display:flex;align-items:center;justify-content:space-between">
                <span style="display:flex;align-items:center;gap:16px"><span class="metric-icon">↯</span><strong>Automatizaciones</strong></span>
                <span class="badge green">{{ $company->automationRules->where('active', true)->count() }} activas</span>
            </summary>
            <form method="post" action="{{ route('marketing.settings.automations.store') }}" class="form-grid" style="margin-top:18px">
                @csrf
                <input type="hidden" name="company_id" value="{{ $company->id }}">
                <div class="field">
                    <label>Nombre</label>
                    <input class="input" name="name" placeholder="Responder fuera de horario" required>
                </div>
                <div class="field">
                    <label>Campaña</label>
                    <select class="select" name="campaign_id">
                        <option value="">Todas</option>
                        @foreach($company->campaigns as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Disparador</label>
                    <input class="input" name="trigger" value="mensaje_recibido" required>
                </div>
                <div class="field">
                    <label>Acción</label>
                    <input class="input" name="action" value="enviar_respuesta" required>
                </div>
                <div class="field full">
                    <label>Mensaje</label>
                    <textarea name="payload_message" rows="3" placeholder="Gracias por escribirnos. En breve te atenderemos."></textarea>
                </div>
                <label class="field" style="display:flex;align-items:center;gap:10px"><input type="checkbox" name="active" value="1" checked> Activa</label>
                <div class="field"><button class="btn" type="submit">Crear automatización</button></div>
            </form>
        </details>

        <details class="panel pad">
            <summary style="cursor:pointer;display:flex;align-items:center;justify-content:space-between">
                <span style="display:flex;align-items:center;gap:16px"><span class="metric-icon">◇</span><strong>Etiquetas</strong></span>
                <span class="badge green">{{ $company->tags->count() }} etiquetas</span>
            </summary>
            <div class="filters" style="margin:16px 0">
                @foreach($company->tags as $tag)
                    <span class="badge green" style="background:{{ $tag->color }}22;color:{{ $tag->color }}">{{ $tag->name }}</span>
                @endforeach
            </div>
            <form method="post" action="{{ route('marketing.settings.tags.store') }}" class="selector-row">
                @csrf
                <input type="hidden" name="company_id" value="{{ $company->id }}">
                <input class="input" name="name" placeholder="Cliente VIP" required>
                <input class="input" type="color" name="color" value="#22c55e">
                <button class="btn" type="submit">Agregar etiqueta</button>
            </form>
        </details>

        <details class="panel pad" id="marketing-office-hours" @if(request('section') === 'hours') open @endif>
            <summary style="cursor:pointer;display:flex;align-items:center;justify-content:space-between">
                <span style="display:flex;align-items:center;gap:16px"><span class="metric-icon">◷</span><strong>Horarios de atención</strong></span>
                <span class="badge green">Lun - Vie 09:00 - 18:00</span>
            </summary>
            <form method="post" action="{{ route('marketing.settings.hours.update') }}" style="margin-top:18px">
                @csrf @method('patch')
                <input type="hidden" name="company_id" value="{{ $company->id }}">
                @foreach([1=>'Lunes',2=>'Martes',3=>'Miércoles',4=>'Jueves',5=>'Viernes',6=>'Sábado',7=>'Domingo'] as $day => $label)
                    @php($hours = $officeHours->firstWhere('day_of_week', $day))
                    <div class="selector-row" style="margin-bottom:10px">
                        <label style="display:flex;align-items:center;gap:10px"><input type="checkbox" name="hours[{{ $day }}][enabled]" value="1" {{ old("hours.$day.enabled", $hours?->enabled ?? $day <= 5) ? 'checked' : '' }}> {{ $label }}</label>
                        <input class="input" type="time" name="hours[{{ $day }}][opens_at]" value="{{ old("hours.$day.opens_at", $hours?->opens_at ?: '09:00') }}">
                        <input class="input" type="time" name="hours[{{ $day }}][closes_at]" value="{{ old("hours.$day.closes_at", $hours?->closes_at ?: '18:00') }}">
                    </div>
                @endforeach
                <button class="btn" type="submit">Guardar horarios</button>
            </form>
        </details>
    </div>

    <aside class="panel pad detail-panel">
        <h2>Resumen de conexión</h2>
        <div class="detail-section">
            <h3>Estado general</h3>
            <div style="display:flex;gap:12px;align-items:center">
                <span class="metric-icon">✓</span>
                <div><strong class="trend">Todo funcionando</strong><br><span class="muted small">Tus integraciones y servicios están activos.</span></div>
            </div>
        </div>
        <div class="detail-section">
            <h3>Cloud API configurados</h3>
            <strong style="font-size:28px">{{ $company->whatsappNumbers->whereNotNull('meta_phone_number_id')->count() }}</strong>
            @foreach($company->whatsappNumbers as $number)
                <div class="detail-row"><span>{{ $number->phone_number }}</span><span class="badge {{ $number->last_webhook_at ? 'green' : '' }}">{{ $number->last_webhook_at ? 'Webhook recibido' : ($number->hasCloudConfiguration() ? 'Configurado' : ($number->status === 'pending_meta' ? 'Pendiente de Meta' : 'Interno')) }}</span></div>
            @endforeach
        </div>
        <div class="detail-section">
            <h3>Webhook / API</h3>
            <p class="muted small">Endpoint webhook: <strong>{{ url('/api/webhooks/whatsapp') }}</strong></p>
            <p class="muted small">La URL debe ser pública y usar HTTPS para Meta.</p>
        </div>
        <div class="detail-section">
            <h3>Entorno</h3>
            <strong>Cloud API</strong>
            <p class="muted small">La recepción depende de un webhook público HTTPS y de los secretos configurados en el servidor.</p>
        </div>
        <div class="panel pad" style="background:#f0fdf4">
            <strong>¿Necesitas ayuda?</strong>
            <p class="muted small">Revisa el README del proyecto para instalación, API y datos demo.</p>
        </div>
    </aside>
</div>
@endsection
