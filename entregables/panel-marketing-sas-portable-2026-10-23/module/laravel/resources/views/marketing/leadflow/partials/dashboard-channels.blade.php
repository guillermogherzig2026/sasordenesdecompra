<div class="marketing-dashboard-content" @if($dashboardView !== 'channels') hidden @endif>
    <nav class="marketing-settings-nav" aria-label="Menú de configuraciones">
        <a href="#marketing-channels" aria-current="location"><x-marketing-icon name="settings" />Canales</a>
        @if($can('campaigns'))<a href="#marketing-link-campaign"><x-marketing-icon name="link" />Vincular campaña</a>@endif
        @if($can('settings') && $company)
            <a href="{{ route('marketing.settings.index', $companyParameters) }}"><x-marketing-icon name="company" />Datos de empresa</a>
            <a href="{{ route('marketing.settings.index', array_merge($companyParameters, ['section' => 'automations'])) }}#marketing-automations"><x-marketing-icon name="automation" />Automatizaciones</a>
        @endif
    </nav>

    <section class="panel marketing-channel-panel" id="marketing-channels" aria-labelledby="marketing-channels-title">
        <header class="marketing-channel-heading">
            <div><h2 id="marketing-channels-title">Canales e integraciones</h2><p>Configura las cuentas de {{ $company?->name ?? 'tu empresa' }} y asígnalas a sus campañas.</p></div>
            <div class="marketing-channel-tools">
                <span class="marketing-company-pill">Empresa: <strong>{{ $company?->name ?? 'Todas' }}</strong></span>
                <label class="marketing-channel-search"><x-marketing-icon name="search" /><input type="search" placeholder="Buscar canal" aria-label="Buscar canal" data-marketing-channel-search></label>
            </div>
        </header>
        @if($company)
            <div class="marketing-configuration-summary"><span><strong>{{ $configuredChannelCount }}</strong> canales configurados</span><span><strong>{{ $linkedCampaignCount }}</strong> campañas con canales</span><span>1. Configura una cuenta <x-marketing-icon name="arrow" /> 2. Vincula una campaña</span></div>
        @else
            <p class="marketing-channel-notice">Selecciona una empresa en el menú superior para configurar sus cuentas.</p>
        @endif
        <div class="marketing-channel-grid">
            @foreach($channels as $channel)
                @php
                    $key = $channel['key'];
                    $registered = $channelConfigurations[$key] ?? [];
                    $isConfigured = in_array($key, $configuredChannelKeys, true);
                    $status = !$company ? 'Elige empresa' : ($key === 'whatsapp' ? ($whatsappReceivedCount ? 'Actividad recibida' : ($isConfigured ? 'Configurado' : 'Sin configurar')) : ($isConfigured ? 'Cuenta registrada' : 'Sin configurar'));
                    $canConfigure = $company && ($key === 'whatsapp' ? auth()->user()->role === 'superadmin' : $can('settings'));
                @endphp
                <article class="marketing-channel-card" data-marketing-channel-card data-channel-name="{{ $channel['name'] }}">
                    <div class="marketing-channel-card-top">
                        <span class="marketing-channel-logo is-{{ $key }}"><x-marketing-icon :name="$key" /></span>
                        <div class="marketing-channel-copy"><h3>{{ $channel['name'] }}</h3><p>{{ $channel['description'] }}</p></div>
                        <span class="marketing-channel-status {{ $isConfigured ? 'is-ready' : '' }}">{{ $status }}</span>
                    </div>
                    <p class="marketing-channel-account">{{ $key === 'whatsapp' ? ($cloudNumber?->phone_number ?: 'Número oficial de la empresa') : ($registered['account_name'] ?? 'Agrega la cuenta de tu empresa') }}</p>
                    <button class="marketing-channel-action" type="button" data-channel-dialog-open="{{ $key }}" aria-label="{{ $canConfigure ? 'Configurar' : 'Ver opciones de' }} {{ $channel['name'] }}"><x-marketing-icon name="settings" />{{ $canConfigure ? 'Configurar' : 'Ver opciones' }}</button>
                </article>
            @endforeach
        </div>
        <p class="marketing-channel-empty" data-marketing-channel-empty role="status" hidden>No se encontraron canales. Prueba con otro nombre.</p>
    </section>

    @if($can('campaigns'))
        <section class="panel marketing-campaign-link" id="marketing-link-campaign" aria-labelledby="marketing-campaign-link-title">
            <h2 id="marketing-campaign-link-title">Vincular empresa con campaña</h2>
            <p class="marketing-section-caption">Elige la campaña y los canales configurados que utilizará.</p>
            @if($errors->campaignChannels->any())<div class="marketing-form-errors" role="alert"><strong>Revisa la vinculación.</strong><ul>@foreach($errors->campaignChannels->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            @if($company && $campaigns->isNotEmpty())
                <form class="marketing-campaign-link-form" method="post" action="{{ route('marketing.campaigns.channels.update') }}" data-marketing-campaign-form>
                    @csrf
                    <input type="hidden" name="form_context" value="campaign_channels">
                    <div class="marketing-campaign-selection-row">
                        <label for="marketing-link-company">Empresa
                            <select class="select" name="company_id" id="marketing-link-company" data-marketing-link-company data-panel-url="{{ route('marketing.index') }}">
                                @foreach($companies as $item)<option value="{{ $item->id }}" @selected($company->is($item))>{{ $item->name }}</option>@endforeach
                            </select>
                        </label>
                        <label for="marketing-dashboard-campaign">Campaña de marketing
                            <select class="select" name="campaign_id" id="marketing-dashboard-campaign" required data-marketing-campaign-select>
                                <option value="">Seleccionar campaña</option>
                                @foreach($campaigns as $item)<option value="{{ $item->id }}" data-networks="{{ json_encode($item->social_networks ?: []) }}" @selected((string) old('campaign_id', $campaign?->id) === (string) $item->id)>{{ $item->name }}</option>@endforeach
                            </select>
                        </label>
                        <button class="btn primary" type="submit" @disabled(!$configuredChannelCount)><x-marketing-icon name="link" />Vincular a campaña</button>
                    </div>
                    <fieldset class="marketing-campaign-channel-options"><legend>Canales de la campaña</legend>
                        @foreach($channels as $channel)
                            @php($available = in_array($channel['key'], $configuredChannelKeys, true))
                            <label class="marketing-channel-choice" @if(!$available) title="Configura este canal primero" @endif>
                                <input type="checkbox" name="channels[]" value="{{ $channel['key'] }}" data-network="{{ $channelConfigurations[$channel['key']]['network'] ?? $channel['network'] }}" @disabled(!$available) @checked($available && in_array($channel['key'], old('channels', []), true))>
                                <x-marketing-icon :name="$channel['key']" /><span>{{ $channel['name'] }}<small>{{ $available ? ($channelConfigurations[$channel['key']]['account_name'] ?? 'Cloud API configurada') : 'Configura primero' }}</small></span>
                            </label>
                        @endforeach
                    </fieldset>
                    <p class="marketing-campaign-hint">Canales ya registrados: <strong data-marketing-campaign-networks>{{ $campaign ? (implode(', ', $campaign->social_networks ?: []) ?: 'Ninguno') : 'Selecciona una campaña' }}</strong></p>
                    <p class="marketing-campaign-hint">La vinculación agrega los canales seleccionados y conserva los que ya tiene la campaña.</p>
                </form>
            @else
                <div class="marketing-setup-empty"><x-marketing-icon name="campaign" /><div><strong>{{ $company ? 'Crea la primera campaña de '.$company->name : 'Elige una empresa para comenzar' }}</strong><p>{{ $company ? 'Puedes configurar sus cuentas ahora y vincularlas cuando la campaña esté lista.' : 'Cada empresa administra sus propias cuentas y campañas.' }}</p></div>
                    @if($company)<a class="btn primary" href="{{ route('marketing.campaigns.create', ['company_id' => $company->id]) }}">Crear campaña</a>@endif
                </div>
            @endif
        </section>
    @endif

    <div class="marketing-dashboard-shortcuts">
        @if($can('settings') && $company)
            <a href="{{ route('marketing.settings.index', array_merge($companyParameters, ['section' => 'automations'])) }}#marketing-automations"><x-marketing-icon name="automation" /><span><strong>Automatizaciones</strong><small>Reglas y respuestas</small></span><x-marketing-icon name="arrow" /></a>
        @endif
        @if($can('users'))<a href="{{ route('marketing.users.index', $companyParameters) }}"><x-marketing-icon name="users" /><span><strong>Usuarios y permisos</strong><small>Administra tu equipo</small></span><x-marketing-icon name="arrow" /></a>@endif
        @if($can('settings') && $company)<a href="{{ route('marketing.settings.index', array_merge($companyParameters, ['section' => 'hours'])) }}#marketing-office-hours"><x-marketing-icon name="clock" /><span><strong>Horarios de atención</strong><small>Disponibilidad del equipo</small></span><x-marketing-icon name="arrow" /></a>@endif
        @if($can('conversations'))<a href="{{ route('marketing.conversations.index', $scopeParameters) }}"><x-marketing-icon name="conversation" /><span><strong>Conversaciones</strong><small>Atención y seguimiento</small></span><x-marketing-icon name="arrow" /></a>@endif
    </div>
</div>
