@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/agent-center.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/agent-center.js') }}" defer></script>
@endpush

@section('body')
    @php
        $agentData = $agents->mapWithKeys(fn ($agent) => [(string) $agent->id => [
            'id' => $agent->id,
            'name' => $agent->name,
            'description' => $agent->description ?? '',
            'instructions' => $agent->instructions ?? '',
        ]]);
        $openAgentForm = $errors->any() && old('agent_form') === '1';
    @endphp

    <x-app-shell title="Centro de agentes">
        <section
            class="panel agent-center"
            aria-labelledby="agent-center-title"
            data-agent-center
            data-selected="{{ $selected }}"
            data-agent-data-id="agent-center-data"
        >
            <header class="agent-center-heading">
                <div class="agent-center-title">
                    <h2 id="agent-center-title">Centro de agentes</h2>
                    <span class="agent-count" aria-label="{{ $agents->count() }} agentes">{{ $agents->count() }}</span>
                </div>
                <div class="agent-state-legend" aria-label="Estados de los agentes">
                    <span><i class="agent-state-dot" data-active="true" aria-hidden="true"></i> Activo</span>
                    <span><i class="agent-state-dot" data-active="false" aria-hidden="true"></i> Inactivo</span>
                </div>
            </header>

            <div class="agent-carousel">
                <button
                    type="button"
                    class="agent-carousel-arrow"
                    data-agent-carousel="previous"
                    aria-label="Agentes anteriores"
                    title="Agentes anteriores"
                    disabled
                >&#8249;</button>

                <div class="agent-carousel-track" data-agent-track role="group" aria-label="Seleccionar agente">
                    <button
                        type="button"
                        class="agent-choice"
                        data-agent-selection="all"
                        aria-pressed="{{ $selected === 'all' ? 'true' : 'false' }}"
                        aria-controls="agent-information"
                    >
                        <span class="agent-choice-icon agent-choice-grid" aria-hidden="true">&#9638;</span>
                        <span class="agent-choice-name">Todos</span>
                    </button>

                    @foreach ($agents as $agent)
                        <button
                            type="button"
                            class="agent-choice"
                            data-agent-selection="{{ $agent->id }}"
                            data-active="{{ $agent->is_active ? 'true' : 'false' }}"
                            aria-pressed="{{ $selected === (string) $agent->id ? 'true' : 'false' }}"
                            aria-controls="agent-information"
                            aria-describedby="agent-state-{{ $agent->id }}"
                            title="{{ $agent->name }}: {{ $agent->is_active ? 'Activo' : 'Inactivo' }}"
                        >
                            <span class="agent-state-dot agent-choice-state" data-active="{{ $agent->is_active ? 'true' : 'false' }}" aria-hidden="true"></span>
                            <span class="visually-hidden" id="agent-state-{{ $agent->id }}">{{ $agent->is_active ? 'Activo' : 'Inactivo' }}</span>
                            <span class="agent-choice-icon" aria-hidden="true">AI</span>
                            <span class="agent-choice-name">{{ $agent->name }}</span>
                        </button>
                    @endforeach

                    <button type="button" class="agent-choice agent-create" data-agent-create aria-haspopup="dialog">
                        <span class="agent-choice-icon" aria-hidden="true">+</span>
                        <span class="agent-choice-name">Crear nuevo</span>
                    </button>
                </div>

                <button
                    type="button"
                    class="agent-carousel-arrow"
                    data-agent-carousel="next"
                    aria-label="Agentes siguientes"
                    title="Agentes siguientes"
                    disabled
                >&#8250;</button>
            </div>

            <div id="agent-information" class="agent-information-list" aria-live="polite">
                <p class="agent-empty" data-agent-empty hidden>No hay agentes registrados.</p>

                @foreach ($agents as $agent)
                    <article
                        class="agent-information"
                        data-agent-detail="{{ $agent->id }}"
                        @if ($selected !== 'all' && $selected !== (string) $agent->id) hidden @endif
                    >
                        <div class="agent-information-heading">
                            <div class="agent-information-title">
                                <h3>{{ $agent->name }}</h3>
                                <form
                                    method="POST"
                                    action="{{ route('superadmin.agents.toggle', $agent) }}"
                                    class="agent-activation"
                                    data-active="{{ $agent->is_active ? 'true' : 'false' }}"
                                >
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="context" value="{{ $context }}">
                                    <input type="hidden" name="selected" value="{{ $selected !== '' ? $selected : $agent->id }}">
                                    <input type="hidden" name="is_active" value="{{ $agent->is_active ? '0' : '1' }}">
                                    <button
                                        type="submit"
                                        class="agent-switch"
                                        role="switch"
                                        aria-checked="{{ $agent->is_active ? 'true' : 'false' }}"
                                        aria-label="{{ $agent->is_active ? 'Desactivar' : 'Activar' }} agente {{ $agent->name }}"
                                        title="{{ $agent->is_active ? 'Desactivar agente' : 'Activar agente' }}"
                                    >
                                        <span class="agent-switch-text" aria-hidden="true">{{ $agent->is_active ? 'ON' : 'OFF' }}</span>
                                        <span class="agent-switch-thumb" aria-hidden="true"></span>
                                    </button>
                                </form>
                            </div>
                            <button
                                type="button"
                                class="button ghost small agent-edit"
                                data-agent-edit="{{ $agent->id }}"
                                aria-label="Editar agente {{ $agent->name }}"
                            >Editar</button>
                        </div>

                        <dl class="agent-information-grid">
                            <div>
                                <dt>Descripcion</dt>
                                <dd>{{ $agent->description ?? 'Sin descripcion' }}</dd>
                            </div>
                            <div>
                                <dt>Instrucciones</dt>
                                <dd class="agent-instructions" tabindex="0">{{ $agent->instructions ?? 'Sin instrucciones' }}</dd>
                            </div>
                        </dl>
                    </article>
                @endforeach
            </div>
        </section>

        <dialog
            class="agent-modal"
            data-agent-modal
            data-store-url="{{ route('superadmin.agents.store') }}"
            data-update-url="{{ route('superadmin.agents.update', ['aiAgent' => '__AGENT__']) }}"
            data-open-on-load="{{ $openAgentForm ? 'true' : 'false' }}"
            data-editing-id="{{ old('editing_agent_id', '') }}"
        >
            <form method="POST" action="{{ route('superadmin.agents.store') }}" data-agent-form>
                @csrf
                <input type="hidden" name="_method" value="PUT" data-agent-method disabled>
                <input type="hidden" name="context" value="{{ old('context', $context) }}">
                <input type="hidden" name="agent_form" value="1">
                <input type="hidden" name="editing_agent_id" value="{{ old('editing_agent_id', '') }}" data-agent-editing-id>

                <header class="agent-modal-header">
                    <h3 data-agent-form-title>Crear nuevo agente</h3>
                    <button type="button" class="agent-modal-close" data-agent-modal-close aria-label="Cerrar" title="Cerrar">&times;</button>
                </header>

                <div class="agent-form-fields">
                    <label for="agent-name">
                        Nombre
                        <input
                            id="agent-name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            maxlength="120"
                            data-agent-name
                            @error('name') aria-invalid="true" @enderror
                        >
                    </label>
                    @error('name')<p class="agent-error" role="alert">{{ $message }}</p>@enderror

                    <label for="agent-description">
                        Descripcion
                        <textarea
                            id="agent-description"
                            name="description"
                            rows="3"
                            maxlength="1000"
                            data-agent-description
                            @error('description') aria-invalid="true" @enderror
                        >{{ old('description') }}</textarea>
                    </label>
                    @error('description')<p class="agent-error" role="alert">{{ $message }}</p>@enderror

                    <label for="agent-instructions">
                        Instrucciones
                        <textarea
                            id="agent-instructions"
                            name="instructions"
                            rows="8"
                            maxlength="20000"
                            data-agent-instructions
                            @error('instructions') aria-invalid="true" @enderror
                        >{{ old('instructions') }}</textarea>
                    </label>
                    @error('instructions')<p class="agent-error" role="alert">{{ $message }}</p>@enderror
                </div>

                <footer class="agent-modal-actions">
                    <button type="button" class="button ghost" data-agent-modal-close>Cancelar</button>
                    <button type="submit" class="button primary">Guardar</button>
                </footer>
            </form>
        </dialog>

        <script type="application/json" id="agent-center-data">{!! $agentData->toJson(JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
    </x-app-shell>
@endsection
