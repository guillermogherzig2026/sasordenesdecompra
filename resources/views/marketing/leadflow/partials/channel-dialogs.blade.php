@foreach($channels as $channel)
    @php
        $key = $channel['key'];
        $record = $channelConfigurations[$key] ?? [];
        $isCurrentForm = old('configuration_channel') === $key;
        $formErrors = $key === 'whatsapp' ? $errors->getBag('default') : $errors->getBag('channelConfiguration');
        $reopen = $isCurrentForm && $formErrors->any();
        $fieldValue = fn ($field, $fallback = '') => $isCurrentForm ? old($field, $fallback) : $fallback;
    @endphp
    <dialog class="marketing-channel-dialog" id="marketing-channel-{{ $key }}" aria-labelledby="marketing-channel-title-{{ $key }}" data-channel-dialog="{{ $key }}" @if($reopen) data-reopen-dialog @endif>
        <div class="marketing-dialog-heading"><h2 id="marketing-channel-title-{{ $key }}">Configurar {{ $channel['name'] }}</h2><form method="dialog"><button type="submit" aria-label="Cerrar configuración de {{ $channel['name'] }}"><x-marketing-icon name="close" /></button></form></div>
        @if(!$company)
            <p>Selecciona una empresa en el panel para configurar sus cuentas.</p>
        @else
            <span class="marketing-company-pill">Empresa: <strong>{{ $company->name }}</strong></span>
            @if($reopen)<div class="marketing-form-errors" role="alert"><strong>Revisa los datos de la cuenta.</strong><ul>@foreach($formErrors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            @if($key === 'whatsapp')
                @if(auth()->user()->role === 'superadmin')
                    <p class="marketing-channel-notice">Configura el número oficial con los datos de WhatsApp Cloud API. La recepción se confirma cuando llega el primer webhook firmado.</p>
                    <form class="marketing-channel-form" method="post" action="{{ route('marketing.conversations.whatsapp.store') }}" autocomplete="off">
                        @csrf
                        <input type="hidden" name="company_id" value="{{ $company->id }}">
                        <input type="hidden" name="campaign_id" value="{{ $campaign?->id }}">
                        <input type="hidden" name="configuration_channel" value="whatsapp">
                        <input type="hidden" name="return_to" value="dashboard">
                        <div class="marketing-form-grid">
                            <label>Nombre visible<input class="input" name="display_name" maxlength="120" value="{{ $fieldValue('display_name', $cloudNumber?->display_name) }}" placeholder="Atención a clientes"></label>
                            <label>Número oficial<input class="input" name="phone_number" type="tel" maxlength="40" value="{{ $fieldValue('phone_number', $cloudNumber?->phone_number) }}" placeholder="+52 55 1234 5678" required></label>
                            <label>Phone Number ID<input class="input" name="meta_phone_number_id" inputmode="numeric" pattern="[0-9]{5,40}" value="{{ $fieldValue('meta_phone_number_id', $cloudNumber?->meta_phone_number_id) }}" required></label>
                            <label>WABA ID<input class="input" name="meta_waba_id" inputmode="numeric" pattern="[0-9]{5,40}" value="{{ $fieldValue('meta_waba_id', $cloudNumber?->meta_waba_id) }}" required></label>
                            <label class="marketing-form-full">Token de acceso{{ $whatsappHasSavedToken ? ' · deja vacío para conservarlo' : '' }}<input class="input" type="password" name="meta_access_token" value="" autocomplete="new-password" maxlength="2048" @required(!$whatsappHasSavedToken)></label>
                        </div>
                        <div class="marketing-dialog-actions"><button class="btn" type="button" data-channel-dialog-close>Cancelar</button><button class="btn primary" type="submit">Guardar configuración</button></div>
                    </form>
                @else
                    <p>La configuración de WhatsApp está disponible para el superadministrador.</p>
                @endif
                @if($can('conversations'))<a class="btn" href="{{ route('marketing.conversations.index', $companyParameters) }}">Ver conversaciones de WhatsApp</a>@endif
            @elseif($can('settings'))
                <p class="marketing-channel-notice">Registra la cuenta que utilizará esta empresa en sus campañas. Este registro no activa la sincronización ni publica contenido en {{ $channel['name'] }}.</p>
                <form class="marketing-channel-form" method="post" action="{{ route('marketing.settings.channels.update', ['channel' => $key]) }}">
                    @csrf
                    <input type="hidden" name="company_id" value="{{ $company->id }}">
                    <input type="hidden" name="campaign_id" value="{{ $campaign?->id }}">
                    <input type="hidden" name="configuration_channel" value="{{ $key }}">
                    <div class="marketing-form-grid">
                        @if($key === 'other')
                            <label class="marketing-form-full">Plataforma<select class="select" name="network">@foreach(['TikTok', 'LinkedIn', 'YouTube', 'Otros canales'] as $network)<option value="{{ $network }}" @selected($fieldValue('network', $record['network'] ?? 'TikTok') === $network)>{{ $network }}</option>@endforeach</select></label>
                        @endif
                        <label>Nombre de la cuenta<input class="input" name="account_name" value="{{ $fieldValue('account_name', $record['account_name'] ?? '') }}" placeholder="{{ $company->name }}" maxlength="160" required></label>
                        <label>{{ ['meta' => 'ID de la cuenta publicitaria', 'instagram' => 'Usuario o ID de Instagram', 'facebook' => 'Nombre o ID de la página', 'chatgpt' => 'Proyecto o espacio de trabajo', 'x' => 'Usuario o ID de X', 'google' => 'ID de cliente de Google Ads', 'other' => 'Usuario o ID de la cuenta'][$key] }}<input class="input" name="account_identifier" value="{{ $fieldValue('account_identifier', $record['account_identifier'] ?? '') }}" maxlength="180" required></label>
                        <label class="marketing-form-full">Enlace de la cuenta <span>(opcional)</span><input class="input" type="url" name="profile_url" value="{{ $fieldValue('profile_url', $record['profile_url'] ?? '') }}" placeholder="https://" maxlength="2048"></label>
                    </div>
                    <p class="marketing-campaign-hint">Usa el nombre o identificador de la cuenta. No ingreses contraseñas ni claves de acceso.</p>
                    <div class="marketing-dialog-actions"><button class="btn" type="button" data-channel-dialog-close>Cancelar</button><button class="btn primary" type="submit">Guardar configuración</button></div>
                </form>
            @else
                <p>Tu usuario necesita el permiso de Configuración de marketing para editar esta cuenta.</p>
                @if($record)<p>Cuenta registrada: <strong>{{ $record['account_name'] }}</strong></p>@endif
            @endif
        @endif
    </dialog>
@endforeach
