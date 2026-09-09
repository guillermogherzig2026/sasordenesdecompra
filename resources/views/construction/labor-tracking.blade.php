@extends('layouts.app')

@section('body')
    <x-app-shell title="Mano de obra">
        @php
            $selectedProjectId = (int) ($selectedProjectId ?? $carouselProjects->first()?->id);

            if (! $carouselProjects->contains('id', $selectedProjectId)) {
                $selectedProjectId = (int) $carouselProjects->first()?->id;
            }

            $money = fn ($value) => '$'.number_format((float) $value, 2);
            $payrollRows = collect($catalogRows)->where('type', 'Nomina')->values();
            $estimateRows = collect($catalogRows)->where('type', 'Estimacion')->values();
            $selectedProjectName = $carouselProjects->firstWhere('id', $selectedProjectId)?->name ?? 'Sin obra seleccionada';
            $payrollFormContext = old('payroll_form');
            $creatingPayroll = $payrollFormContext === 'create';
            $estimateFormContext = old('estimate_form');
            $creatingEstimate = $estimateFormContext === 'create';
            $invalidPayrollDialogId = null;

            if ($payrollFormContext === 'create') {
                $invalidPayrollDialogId = 'new-payroll-dialog';
            } elseif ($estimateFormContext === 'create') {
                $invalidPayrollDialogId = 'new-estimate-dialog';
            }
        @endphp

        <section class="panel construction-carousel-panel" data-construction-carousel>
            <div class="construction-carousel-header">
                <div class="construction-carousel-title">
                    <span class="construction-carousel-count">{{ $carouselProjects->count() }}</span>
                    <h2>Obras activas y por iniciar</h2>
                </div>
                <a class="button ghost small" href="{{ route('construction.dashboard') }}">Atras</a>
            </div>

            <div class="construction-carousel-shell">
                <button class="construction-carousel-nav" type="button" data-carousel-prev aria-label="Anterior">&lt;</button>

                <div class="construction-carousel-track" data-construction-carousel-track>
                    @if ($carouselProjects->isNotEmpty())
                        <button
                            class="construction-project-tile construction-project-tile-all"
                            type="button"
                            data-labor-project
                            data-labor-all-projects
                            data-project-id="all"
                            data-project-name="Todas las obras"
                            aria-pressed="false"
                            aria-label="Mostrar pagos de todas las obras"
                        >
                            <span class="construction-project-avatar">{{ $carouselProjects->count() }}</span>
                            <span class="construction-project-key">Pagos vigentes</span>
                            <strong class="construction-project-name">Todas</strong>
                            <span class="construction-project-status"><span></span>Ver todos los pagos</span>
                        </button>
                    @endif

                    @forelse ($carouselProjects as $project)
                        @php
                            $isSelectedProject = $project->id === $selectedProjectId;
                        @endphp
                        <button class="construction-project-tile {{ $isSelectedProject ? 'active' : '' }}" type="button" data-labor-project data-project-id="{{ $project->id }}" data-project-name="{{ $project->name }}" data-carousel-project-id="{{ $project->id }}" data-carousel-project-status="{{ $project->status }}" aria-pressed="{{ $isSelectedProject ? 'true' : 'false' }}">
                            <span class="construction-project-avatar">
                                @if ($project->photo_path)
                                    <img src="{{ $project->photo_path }}" alt="">
                                @else
                                    {{ substr($project->project_key, -2) }}
                                @endif
                            </span>
                            <span class="construction-project-key">{{ $project->project_key }}</span>
                            <strong class="construction-project-name">{{ $project->name }}</strong>
                            <span class="construction-project-status"><span></span>{{ $project->status }}</span>
                        </button>
                    @empty
                        <button class="construction-project-tile" type="button" disabled>
                            <span class="construction-project-avatar">OB</span>
                            <span class="construction-project-key">Sin obras</span>
                            <strong class="construction-project-name">No hay obras visibles</strong>
                            <span class="construction-project-status"><span></span>Pendiente</span>
                        </button>
                    @endforelse
                </div>

                <button class="construction-carousel-nav" type="button" data-carousel-next aria-label="Siguiente">&gt;</button>
            </div>
        </section>

        <section class="panel labor-budget-panel" data-no-section-export>
            <div class="construction-carousel-title">
                <span class="construction-carousel-count">2</span>
                <h2>Presupuestos</h2>
            </div>

            <div class="labor-budget-grid">
                <button class="labor-budget-card" type="button" data-labor-catalog-toggle="payroll" aria-expanded="false" aria-controls="labor-payroll-catalog">
                    <span class="labor-budget-icon">NOM</span>
                    <strong>Nomina</strong>
                    <span class="labor-budget-toggle" data-labor-catalog-indicator aria-hidden="true">+</span>
                </button>
                <button class="labor-budget-card" type="button" data-labor-catalog-toggle="estimates" aria-expanded="false" aria-controls="labor-estimate-catalog">
                    <span class="labor-budget-icon">EST</span>
                    <strong>Estimaciones</strong>
                    <span class="labor-budget-toggle" data-labor-catalog-indicator aria-hidden="true">+</span>
                </button>
            </div>

            <div class="labor-payroll-catalog" id="labor-payroll-catalog" data-labor-catalog="payroll" hidden>
                <div class="labor-payroll-header">
                    <div>
                        <h3>Cat&aacute;logo de n&oacute;minas</h3>
                        <p>N&oacute;minas de la obra seleccionada: <strong data-selected-project-name>{{ $selectedProjectName }}</strong></p>
                    </div>
                    <div class="labor-payroll-actions">
                        <button class="button primary small" type="button" data-payroll-create data-supply-detail-open="new-payroll-dialog" @disabled($carouselProjects->isEmpty())>
                            Nueva nomina periodica
                        </button>
                        <button class="labor-catalog-collapse" type="button" data-labor-catalog-close="payroll" title="Ocultar catalogo" aria-label="Ocultar catalogo de nominas">&minus;</button>
                    </div>
                </div>

                <div class="table-scroll labor-catalog-scroll">
                    <table class="labor-catalog-table">
                        <thead>
                            <tr>
                                <th>Codigo</th>
                                <th>Contratista</th>
                                <th>Descripcion</th>
                                <th>Area / categoria</th>
                                <th>Periodicidad</th>
                                <th>Monto presupuestado</th>
                                <th>Monto erogado</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payrollRows as $row)
                                <tr
                                    id="payroll-row-{{ $row['id'] }}"
                                    data-payroll-row
                                    data-payroll-project="{{ $row['project_id'] }}"
                                    @if ((int) $row['project_id'] !== $selectedProjectId) hidden @endif
                                >
                                    <td>{{ $row['code'] }}</td>
                                    <td>{{ $row['responsible'] }}</td>
                                    <td>{{ $row['description'] }}</td>
                                    <td><span class="labor-area-badge">{{ $row['area'] }}</span></td>
                                    <td>{{ $row['periodicity'] }}</td>
                                    <td>{{ $money($row['amount']) }}</td>
                                    <td data-disbursed-amount="{{ number_format($row['disbursed_amount'], 2, '.', '') }}">{{ $money($row['disbursed_amount']) }}</td>
                                    <td data-filter-value="{{ $row['status'] }}">
                                        <details class="status-menu">
                                            <summary
                                                class="status {{ $row['status_class'] }}"
                                                aria-label="Cambiar estatus de {{ $row['code'] }}"
                                            >{{ $row['status'] }}</summary>
                                            <div class="status-menu-panel">
                                                @foreach ($payrollCatalogStatusOptions as $payrollStatus)
                                                    @continue($payrollStatus === $row['status'])
                                                    @php
                                                        $statusOptionClass = match ($payrollStatus) {
                                                            'Concluida' => 'primary',
                                                            'Cancelada' => 'danger',
                                                            default => 'ghost',
                                                        };
                                                    @endphp
                                                    <form
                                                        class="inline-form"
                                                        method="POST"
                                                        action="{{ route('construction.payrolls.status.update', $row['id']) }}"
                                                    >
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="{{ $payrollStatus }}">
                                                        <button
                                                            class="button small {{ $statusOptionClass }}"
                                                            type="submit"
                                                        >{{ $payrollStatus }}</button>
                                                    </form>
                                                @endforeach
                                            </div>
                                        </details>
                                    </td>
                                    <td>
                                        <div class="labor-file-actions">
                                            <a class="button ghost small" href="{{ route('construction.payrolls.edit', $row['id']) }}">Editar</a>
                                            <button
                                                class="button danger small"
                                                type="button"
                                                data-labor-delete
                                                data-labor-delete-url="{{ $row['delete_url'] }}"
                                            >Eliminar</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr data-payroll-empty @if ($payrollRows->contains(fn ($row) => (int) $row['project_id'] === $selectedProjectId)) hidden @endif>
                                <td class="empty-state" colspan="9">No hay nominas registradas para esta obra.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="labor-payroll-catalog labor-estimate-catalog" id="labor-estimate-catalog" data-labor-catalog="estimates" hidden>
                <div class="labor-payroll-header">
                    <div>
                        <h3>Cat&aacute;logo de estimaciones</h3>
                        <p>Estimaciones de la obra seleccionada: <strong data-selected-project-name>{{ $selectedProjectName }}</strong></p>
                    </div>
                    <div class="labor-payroll-actions">
                        <button class="button primary small" type="button" data-estimate-create data-supply-detail-open="new-estimate-dialog" @disabled($carouselProjects->isEmpty())>
                            Nuevo paquete de estimaciones
                        </button>
                        <button class="labor-catalog-collapse" type="button" data-labor-catalog-close="estimates" title="Ocultar catalogo" aria-label="Ocultar catalogo de estimaciones">&minus;</button>
                    </div>
                </div>

                <div class="table-scroll labor-catalog-scroll">
                    <table class="labor-catalog-table">
                        <thead>
                            <tr>
                                <th>Codigo</th>
                                <th>Contratista</th>
                                <th>Descripcion</th>
                                <th>Area / categoria</th>
                                <th>Periodicidad</th>
                                <th>Monto presupuestado</th>
                                <th>Monto erogado</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($estimateRows as $row)
                                <tr data-estimate-row data-estimate-project="{{ $row['project_id'] }}" @if ((int) $row['project_id'] !== $selectedProjectId) hidden @endif>
                                    <td>{{ $row['code'] }}</td>
                                    <td>{{ $row['responsible'] }}</td>
                                    <td>{{ $row['description'] }}</td>
                                    <td><span class="labor-area-badge">{{ $row['area'] }}</span></td>
                                    <td>{{ $row['periodicity'] }}</td>
                                    <td>{{ $money($row['amount']) }}</td>
                                    <td data-disbursed-amount="{{ number_format($row['disbursed_amount'], 2, '.', '') }}">{{ $money($row['disbursed_amount']) }}</td>
                                    <td><span class="status {{ $row['status_class'] }}">{{ $row['status'] }}</span></td>
                                    <td>
                                        <div class="labor-file-actions">
                                            <button class="button ghost small" type="button">Editar</button>
                                            <button
                                                class="button danger small"
                                                type="button"
                                                data-labor-delete
                                                data-labor-delete-url="{{ $row['delete_url'] }}"
                                            >Eliminar</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr data-estimate-empty @if ($estimateRows->contains(fn ($row) => (int) $row['project_id'] === $selectedProjectId)) hidden @endif>
                                <td class="empty-state" colspan="9">No hay estimaciones registradas para esta obra.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="panel labor-tracking-panel">
            <h2 class="labor-tracking-title">Pagos Vigentes</h2>

            <div class="labor-toolbar">
                <div class="labor-tabs" role="tablist" aria-label="Seguimiento de mano de obra">
                    <button class="labor-tab is-active" type="button" data-labor-filter="all">Todos</button>
                    <button class="labor-tab" type="button" data-labor-filter="nomina">Nomina Quincenal</button>
                    <button class="labor-tab" type="button" data-labor-filter="estimacion">Estimaciones quincenales</button>
                </div>
                <div class="labor-toolbar-note">
                    <span class="fine-print" data-labor-scope-note>Pendientes de pago de la obra seleccionada</span>
                    <a
                        class="button ghost"
                        data-labor-history-link
                        data-history-base="{{ route('construction.placeholder', ['section' => 'pagos']) }}"
                        href="{{ route('construction.placeholder', ['section' => 'pagos', 'project' => $selectedProjectId]) }}"
                    >Historial</a>
                </div>
            </div>

            <div class="table-scroll labor-table-scroll">
                <table class="labor-table">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Codigo</th>
                            <th>Obra</th>
                            <th>Descripcion / alcance</th>
                            <th>Area / categoria</th>
                            <th>Responsable</th>
                            <th>Periodo / referencia</th>
                            <th>Fecha limite de pago</th>
                            <th>% Avance</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Factura</th>
                            <th>Pago</th>
                            <th>Fecha pago</th>
                            <th class="labor-actions-column" data-no-filter data-no-sort>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($laborRows as $row)
                            <tr data-labor-row="{{ strtolower($row['type']) }}" data-labor-project-id="{{ $row['project_id'] }}" data-labor-code="{{ $row['code'] }}" @if ((int) $row['project_id'] !== $selectedProjectId) hidden @endif>
                                <td><span class="labor-type-badge {{ strtolower($row['type']) }}">{{ $row['type'] }}</span></td>
                                <td>{{ $row['code'] }}</td>
                                <td class="labor-project-cell">
                                    <strong>{{ $row['project_key'] }}</strong>
                                    <span title="{{ $row['project_name'] }}">{{ $row['project_name'] }}</span>
                                </td>
                                <td><strong>{{ $row['description'] }}</strong></td>
                                <td><span class="labor-area-badge">{{ $row['area'] }}</span></td>
                                <td>{{ $row['responsible'] }}</td>
                                <td>{{ $row['period'] }}</td>
                                <td>{{ $row['payment_due_date'] }}</td>
                                <td>
                                    <div class="labor-progress">
                                        <strong>{{ $row['progress'] }}%</strong>
                                        <span class="labor-progress-track"><span style="width: {{ $row['progress'] }}%;"></span></span>
                                    </div>
                                </td>
                                <td>{{ $money($row['amount']) }}</td>
                                <td><span class="status {{ $row['status_class'] }}">{{ $row['status'] }}</span></td>
                                <td>
                                    <div class="labor-file-actions">
                                        <button
                                            class="button ghost small invoice-upload-button invoice-upload-{{ $row['invoice_document_status'] }}"
                                            type="button"
                                            title="Subir documentos de factura ({{ $row['invoice_document_count'] }}/3 archivos)"
                                            aria-label="Subir documentos de factura ({{ $row['invoice_document_count'] }}/3 archivos)"
                                            data-invoice-document-status="{{ $row['invoice_document_status'] }}"
                                            data-invoice-document-count="{{ $row['invoice_document_count'] }}"
                                            data-supply-detail-open="invoice-documents-dialog-{{ $row['payment_order_id'] }}"
                                        >Subir</button>
                                        @if (filled($row['invoice_file_url']))
                                            <a class="button ghost small labor-view-button" href="{{ $row['invoice_file_url'] }}" target="_blank" rel="noopener">Ver</a>
                                        @else
                                            <button class="button ghost small labor-view-button" type="button" disabled aria-disabled="true" title="Sin archivo adjunto">Ver</button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="labor-file-actions">
                                        @if (auth()->user()?->canAccessRole('finance'))
                                            <form method="POST" action="{{ $row['payment_upload_url'] }}" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="paid_on" value="{{ now()->toDateString() }}">
                                                <label class="button ghost small" title="Subir comprobante de pago">
                                                    Subir
                                                    <input class="file-upload-input" type="file" name="payment_file" accept=".pdf,.jpg,.jpeg,.png,.webp" data-auto-file-submit required>
                                                </label>
                                            </form>
                                        @else
                                            <button class="button ghost small" type="button" disabled aria-disabled="true" title="Disponible para Finanzas">Subir</button>
                                        @endif
                                        @if (filled($row['payment_file_url']))
                                            <a class="button ghost small labor-view-button" href="{{ $row['payment_file_url'] }}" target="_blank" rel="noopener">Ver</a>
                                        @else
                                            <button class="button ghost small labor-view-button" type="button" disabled aria-disabled="true" title="Sin archivo adjunto">Ver</button>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $row['payment_date'] }}</td>
                                <td class="labor-actions-column">
                                    <button
                                        class="button danger small"
                                        type="button"
                                        data-labor-delete
                                        data-labor-delete-url="{{ $row['delete_url'] }}"
                                    >Eliminar</button>
                                </td>
                            </tr>
                        @endforeach
                        <tr data-labor-empty hidden>
                            <td class="empty-state" colspan="15">No hay pagos vigentes para esta obra.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        @foreach ($laborRows as $row)
            @php
                $invoiceDialogId = 'invoice-documents-dialog-'.$row['payment_order_id'];
                $progressPhotoDialogId = 'progress-photo-dialog-'.$row['payment_order_id'];
            @endphp
            <dialog
                class="supply-detail-dialog invoice-documents-dialog"
                id="{{ $invoiceDialogId }}"
                data-supply-detail-dialog
                aria-labelledby="{{ $invoiceDialogId }}-title"
            >
                <div class="supply-detail-card invoice-documents-card">
                    <button class="supply-detail-close" type="button" data-supply-detail-close aria-label="Cerrar">&times;</button>

                    <div class="invoice-documents-heading">
                        <span class="invoice-documents-heading-icon" aria-hidden="true">DOC</span>
                        <div>
                            <h3 id="{{ $invoiceDialogId }}-title">Documentos de factura y avance</h3>
                            <p class="fine-print">{{ $row['code'] }} &middot; {{ $row['description'] }}</p>
                        </div>
                    </div>

                    @if (session('invoice_document_status') && session('open_invoice_dialog') === $invoiceDialogId)
                        <p class="invoice-documents-inline-status" role="status">
                            <span aria-hidden="true">&#10003;</span>
                            {{ session('invoice_document_status') }}
                        </p>
                    @endif

                    <div class="invoice-document-list">
                        <section class="invoice-document-row">
                            <span class="invoice-document-completion-slot">
                                @if (filled($row['invoice_file_url']))
                                    <span class="invoice-document-complete-marker" role="img" aria-label="Factura en PDF cargada" title="Archivo cargado" data-document-complete="invoice-pdf">&#10003;</span>
                                @endif
                            </span>
                            <span class="invoice-document-kind">PDF</span>
                            <div class="invoice-document-copy">
                                <strong>Factura en PDF</strong>
                                <span>{{ $row['invoice_file_name'] ?: 'Archivo pendiente' }}</span>
                                @if (old('invoice_dialog') === $invoiceDialogId)
                                    @error('invoice_file')
                                        <small class="form-error">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>
                            <div class="invoice-document-actions">
                                <form method="POST" action="{{ $row['invoice_upload_url'] }}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="invoice_dialog" value="{{ $invoiceDialogId }}">
                                    <button class="button primary small" type="button" data-file-picker-target="invoice-pdf-{{ $row['payment_order_id'] }}">Subir factura PDF</button>
                                    <input class="file-upload-input" id="invoice-pdf-{{ $row['payment_order_id'] }}" type="file" name="invoice_file" accept=".pdf,application/pdf" data-auto-file-submit required>
                                </form>
                                @if (filled($row['invoice_file_url']))
                                    <a class="button ghost small" href="{{ $row['invoice_file_url'] }}" target="_blank" rel="noopener">Ver</a>
                                @endif
                            </div>
                        </section>

                        <section class="invoice-document-row">
                            <span class="invoice-document-completion-slot">
                                @if (filled($row['invoice_xml_file_url']))
                                    <span class="invoice-document-complete-marker" role="img" aria-label="XML de la factura cargado" title="Archivo cargado" data-document-complete="invoice-xml">&#10003;</span>
                                @endif
                            </span>
                            <span class="invoice-document-kind xml">XML</span>
                            <div class="invoice-document-copy">
                                <strong>XML de la factura</strong>
                                <span>{{ $row['invoice_xml_file_name'] ?: 'Archivo pendiente' }}</span>
                                @if (old('invoice_dialog') === $invoiceDialogId)
                                    @error('invoice_xml_file')
                                        <small class="form-error">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>
                            <div class="invoice-document-actions">
                                <form method="POST" action="{{ $row['invoice_upload_url'] }}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="invoice_dialog" value="{{ $invoiceDialogId }}">
                                    <button class="button primary small" type="button" data-file-picker-target="invoice-xml-{{ $row['payment_order_id'] }}">Subir XML</button>
                                    <input class="file-upload-input" id="invoice-xml-{{ $row['payment_order_id'] }}" type="file" name="invoice_xml_file" accept=".xml,application/xml,text/xml" data-auto-file-submit required>
                                </form>
                                @if (filled($row['invoice_xml_file_url']))
                                    <a class="button ghost small" href="{{ $row['invoice_xml_file_url'] }}" target="_blank" rel="noopener">Ver</a>
                                @endif
                            </div>
                        </section>

                        <section class="invoice-document-row">
                            <span class="invoice-document-completion-slot">
                                @if (filled($row['fiscal_verification_file_url']))
                                    <span class="invoice-document-complete-marker" role="img" aria-label="Verificacion fiscal en PDF cargada" title="Archivo cargado" data-document-complete="fiscal-verification-pdf">&#10003;</span>
                                @endif
                            </span>
                            <span class="invoice-document-kind fiscal">PDF</span>
                            <div class="invoice-document-copy">
                                <strong>Verificaci&oacute;n fiscal en PDF</strong>
                                <span>{{ $row['fiscal_verification_file_name'] ?: 'Archivo pendiente' }}</span>
                                @if (old('invoice_dialog') === $invoiceDialogId)
                                    @error('fiscal_verification_file')
                                        <small class="form-error">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>
                            <div class="invoice-document-actions">
                                <form method="POST" action="{{ $row['invoice_upload_url'] }}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="invoice_dialog" value="{{ $invoiceDialogId }}">
                                    <button class="button primary small" type="button" data-file-picker-target="fiscal-verification-{{ $row['payment_order_id'] }}">Subir verificaci&oacute;n fiscal PDF</button>
                                    <input class="file-upload-input" id="fiscal-verification-{{ $row['payment_order_id'] }}" type="file" name="fiscal_verification_file" accept=".pdf,application/pdf" data-auto-file-submit required>
                                </form>
                                @if (filled($row['fiscal_verification_file_url']))
                                    <a class="button ghost small" href="{{ $row['fiscal_verification_file_url'] }}" target="_blank" rel="noopener">Ver</a>
                                @endif
                            </div>
                        </section>

                    </div>

                    <section class="progress-photo-section" aria-labelledby="{{ $invoiceDialogId }}-photos-title">
                        <div class="progress-photo-heading">
                            <span class="invoice-document-completion-slot">
                                @if ($row['progress_photos'])
                                    <span class="invoice-document-complete-marker" role="img" aria-label="Fotos del avance cargadas" title="Archivos cargados" data-document-complete="progress-photos">&#10003;</span>
                                @endif
                            </span>
                            <div class="progress-photo-heading-summary">
                                <div class="progress-photo-heading-copy">
                                    <h4 id="{{ $invoiceDialogId }}-photos-title">Fotos del avance</h4>
                                    <span>{{ count($row['progress_photos']) === 1 ? '1 foto' : count($row['progress_photos']).' fotos' }}</span>
                                </div>
                            </div>
                            <button class="button primary small progress-photo-add-button" type="button" data-progress-photo-open="{{ $progressPhotoDialogId }}">
                                <span class="progress-photo-add-icon" aria-hidden="true">+</span>
                                Agregar fotos
                            </button>
                        </div>

                        @if ($row['progress_photos'])
                            <div class="progress-photo-grid" aria-label="Fotos de avance cargadas">
                                @foreach ($row['progress_photos'] as $photo)
                                    <article class="progress-photo-card" data-progress-photo-card>
                                        <a
                                            class="progress-photo-image-link"
                                            href="{{ $photo['url'] }}"
                                            target="_blank"
                                            rel="noopener"
                                            title="Ver {{ $photo['name'] }}"
                                            aria-label="Ver foto de avance {{ $loop->iteration }}: {{ $photo['name'] }}"
                                        >
                                            <img src="{{ $photo['url'] }}" alt="{{ $photo['description'] ?: 'Foto de avance '.$loop->iteration }}" loading="lazy">
                                        </a>
                                        <p class="progress-photo-description {{ filled($photo['description']) ? '' : 'is-pending' }}">
                                            {{ $photo['description'] ?: 'Descripcion pendiente.' }}
                                        </p>
                                        <div class="progress-photo-card-actions">
                                            <button class="progress-photo-text-action" type="button" data-progress-photo-edit-toggle>Editar</button>
                                            <form method="POST" action="{{ $photo['delete_url'] }}" data-progress-photo-delete-form>
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="invoice_dialog" value="{{ $invoiceDialogId }}">
                                                <button class="progress-photo-text-action danger" type="submit">Eliminar</button>
                                            </form>
                                        </div>
                                        <form class="progress-photo-edit-form" method="POST" action="{{ $photo['update_url'] }}" data-progress-photo-edit-form hidden>
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="invoice_dialog" value="{{ $invoiceDialogId }}">
                                            <label>
                                                Descripcion
                                                <textarea name="description" rows="3" maxlength="500" required>{{ $photo['description'] }}</textarea>
                                            </label>
                                            <div class="progress-photo-edit-actions">
                                                <button class="button ghost small" type="button" data-progress-photo-edit-cancel>Cancelar</button>
                                                <button class="button primary small" type="submit">Guardar</button>
                                            </div>
                                        </form>
                                    </article>
                                @endforeach
                            </div>
                        @else
                            <p class="progress-photo-empty">Todavia no hay fotos de avance.</p>
                        @endif

                        @if (old('invoice_dialog') === $invoiceDialogId)
                            @error('description')
                                <small class="form-error">{{ $message }}</small>
                            @enderror
                        @endif
                        <p class="progress-photo-help">Puedes agregar todas las fotos que necesites.</p>
                    </section>

                    <div class="invoice-documents-footer">
                        <button class="button ghost" type="button" data-supply-detail-close>Cerrar</button>
                        <button class="button primary" type="button" data-invoice-documents-save data-supply-detail-close>Guardar</button>
                    </div>
                </div>
            </dialog>

            <dialog
                class="supply-detail-dialog progress-photo-dialog"
                id="{{ $progressPhotoDialogId }}"
                data-supply-detail-dialog
                data-progress-photo-dialog
                data-return-dialog="{{ $invoiceDialogId }}"
                aria-labelledby="{{ $progressPhotoDialogId }}-title"
            >
                <form class="supply-detail-card progress-photo-dialog-card" method="POST" action="{{ $row['progress_photos_upload_url'] }}" enctype="multipart/form-data" data-progress-photo-form>
                    @csrf
                    <input type="hidden" name="invoice_dialog" value="{{ $invoiceDialogId }}">
                    <input type="hidden" name="progress_photo_dialog" value="{{ $progressPhotoDialogId }}">
                    <input class="file-upload-input" type="file" name="progress_photos[]" data-progress-photo-submit-files multiple hidden>
                    <button class="supply-detail-close" type="button" data-supply-detail-close aria-label="Cerrar">&times;</button>

                    <div class="progress-photo-dialog-heading">
                        <h3 id="{{ $progressPhotoDialogId }}-title" data-progress-photo-dialog-title>Agregar fotos</h3>
                        <p class="fine-print" data-progress-photo-dialog-subtitle>{{ $row['code'] }} &middot; Fotos del avance</p>
                    </div>

                    @if (old('progress_photo_dialog') === $progressPhotoDialogId)
                        <div class="form-error progress-photo-server-errors">
                            @error('progress_photos')<span>{{ $message }}</span>@enderror
                            @error('progress_photos.*')<span>{{ $message }}</span>@enderror
                            @error('progress_descriptions')<span>{{ $message }}</span>@enderror
                            @error('progress_descriptions.*')<span>{{ $message }}</span>@enderror
                        </div>
                    @endif

                    <div class="progress-photo-stage" data-progress-photo-stage="select">
                        <div class="progress-photo-drop-zone" data-progress-photo-drop-zone tabindex="0" role="button" aria-label="Seleccionar fotos del avance">
                            <span class="progress-photo-upload-symbol" aria-hidden="true">&uarr;</span>
                            <strong>Arrastra tus fotos aqui</strong>
                            <button class="button primary progress-photo-select-button" type="button" data-progress-photo-select>
                                <span aria-hidden="true">+</span>
                                Seleccionar fotos
                            </button>
                            <span>Selecciona una o varias imagenes.</span>
                        </div>
                        <input class="file-upload-input" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" data-progress-photo-picker multiple hidden>
                        <input class="file-upload-input" type="file" accept="image/*" capture="environment" data-progress-photo-camera hidden>
                        <button class="button ghost progress-photo-camera-button" type="button" data-progress-photo-camera-button>Tomar foto</button>
                        <p class="progress-photo-info">Cada foto llevara su propia descripcion.</p>
                        <p class="form-error" data-progress-photo-client-error hidden></p>
                        <div class="progress-photo-stage-actions">
                            <button class="button ghost" type="button" data-progress-photo-cancel>Cancelar</button>
                        </div>
                    </div>

                    <div class="progress-photo-stage" data-progress-photo-stage="describe" hidden>
                        <div class="progress-photo-selected-list" data-progress-photo-selected-list></div>
                        <p class="progress-photo-required-note"><span aria-hidden="true">*</span> La descripcion es obligatoria en cada foto.</p>
                        <button class="button ghost small progress-photo-more-button" type="button" data-progress-photo-more>
                            <span aria-hidden="true">+</span>
                            Agregar mas fotos
                        </button>
                        <p class="form-error" data-progress-photo-client-error hidden></p>
                        <div class="progress-photo-stage-actions">
                            <button class="button ghost" type="button" data-progress-photo-cancel>Cancelar</button>
                            <button class="button primary" type="submit" data-progress-photo-save disabled>Guardar fotos</button>
                        </div>
                    </div>
                </form>
            </dialog>
        @endforeach

        <dialog class="confirm-dialog" data-labor-delete-dialog aria-labelledby="labor-delete-title">
            <div class="confirm-card">
                <h3 id="labor-delete-title">Eliminar registro</h3>
                <p>Estas seguro que quieres eliminar?</p>
                <div class="form-actions">
                    <button class="button ghost" type="button" data-labor-delete-no>No</button>
                    <button class="button danger" type="button" data-labor-delete-yes>Si</button>
                </div>
            </div>
        </dialog>

        <form method="POST" data-labor-delete-form hidden>
            @csrf
            @method('DELETE')
        </form>

        @php
            $createPayrollValues = [
                'construction_project_id' => $creatingPayroll ? old('construction_project_id', $selectedProjectId) : $selectedProjectId,
        'code' => $nextPayrollCode,
                'contractor' => $creatingPayroll ? old('contractor', '') : '',
                'description' => $creatingPayroll ? old('description', '') : '',
                'area' => $creatingPayroll ? old('area', 'Mano de obra') : 'Mano de obra',
                'periodicity' => $creatingPayroll ? old('periodicity', 'Quincenal') : 'Quincenal',
                'period_start' => $creatingPayroll ? old('period_start', '') : '',
                'period_end' => $creatingPayroll ? old('period_end', '') : '',
                'period_end_indefinite' => $creatingPayroll ? old('period_end_indefinite', '1') : '1',
                'progress' => $creatingPayroll ? old('progress', 0) : 0,
                'amount' => $creatingPayroll ? old('amount', 0) : 0,
                'status' => $creatingPayroll ? old('status', 'Borrador') : 'Borrador',
                'payment_due_date' => $creatingPayroll ? old('payment_due_date', '') : '',
            ];
        @endphp

        <dialog class="supply-detail-dialog payroll-dialog" id="new-payroll-dialog" data-supply-detail-dialog>
            <div class="supply-detail-card">
                <button class="supply-detail-close" type="button" data-supply-detail-close aria-label="Cerrar">x</button>
                <div>
                    <h3>Nueva nomina periodica</h3>
                    <p class="fine-print">Registra el periodo, contratista y monto de la nomina para la obra seleccionada.</p>
                </div>

                <form class="payroll-form" method="POST" action="{{ route('construction.payrolls.store') }}">
                    @csrf
                    <input type="hidden" name="payroll_form" value="create">
                    @include('construction.partials.payroll-form-fields', [
                        'activeProjects' => $carouselProjects,
                        'values' => $createPayrollValues,
                        'isCreateForm' => true,
                    ])

                    <div class="form-actions payroll-form-actions">
                        <button class="button ghost" type="button" data-supply-detail-close>Cancelar</button>
                        <button class="button primary" type="submit">Guardar nomina</button>
                    </div>
                </form>
            </div>
        </dialog>

        @php
            $createEstimateValues = [
                'construction_project_id' => $creatingEstimate ? old('construction_project_id', $selectedProjectId) : $selectedProjectId,
                'code' => $creatingEstimate ? old('code', '') : '',
                'contractor' => $creatingEstimate ? old('contractor', '') : '',
                'description' => $creatingEstimate ? old('description', '') : '',
                'area' => $creatingEstimate ? old('area', '') : '',
                'periodicity' => $creatingEstimate ? old('periodicity', 'Quincenal') : 'Quincenal',
                'period_reference' => $creatingEstimate ? old('period_reference', '') : '',
                'payment_due_date' => $creatingEstimate ? old('payment_due_date', '') : '',
                'progress' => $creatingEstimate ? old('progress', 0) : 0,
                'amount' => $creatingEstimate ? old('amount', 0) : 0,
                'status' => $creatingEstimate ? old('status', 'Sin asignar') : 'Sin asignar',
            ];
        @endphp

        <dialog class="supply-detail-dialog payroll-dialog" id="new-estimate-dialog" data-supply-detail-dialog>
            <div class="supply-detail-card">
                <button class="supply-detail-close" type="button" data-supply-detail-close aria-label="Cerrar">x</button>
                <div>
                    <h3>Nuevo paquete de estimaciones</h3>
                    <p class="fine-print">Registra el periodo, contratista y monto de la estimacion para la obra seleccionada.</p>
                </div>

                <form class="payroll-form" method="POST" action="{{ route('construction.estimates.store') }}">
                    @csrf
                    <input type="hidden" name="estimate_form" value="create">
                    @include('construction.partials.estimate-form-fields', [
                        'activeProjects' => $carouselProjects,
                        'values' => $createEstimateValues,
                    ])

                    <div class="form-actions payroll-form-actions">
                        <button class="button ghost" type="button" data-supply-detail-close>Cancelar</button>
                        <button class="button primary" type="submit">Guardar paquete</button>
                    </div>
                </form>
            </div>
        </dialog>

        <script>
            (() => {
                document.querySelectorAll('[data-construction-carousel]').forEach((carousel) => {
                    const track = carousel.querySelector('[data-construction-carousel-track]');
                    const scrollByTile = () => Math.max(220, Math.round((track?.clientWidth || 260) * 0.72));

                    carousel.querySelector('[data-carousel-prev]')?.addEventListener('click', () => {
                        track?.scrollBy({ left: -scrollByTile(), behavior: 'smooth' });
                    });

                    carousel.querySelector('[data-carousel-next]')?.addEventListener('click', () => {
                        track?.scrollBy({ left: scrollByTile(), behavior: 'smooth' });
                    });
                });

                const projectButtons = document.querySelectorAll('[data-labor-project]');
                const selectedProjectNames = document.querySelectorAll('[data-selected-project-name]');
                const payrollRows = [...document.querySelectorAll('[data-payroll-row]')];
                const estimateRows = [...document.querySelectorAll('[data-estimate-row]')];
                let laborRows = [...document.querySelectorAll('[data-labor-row]')];
                const payrollEmpty = document.querySelector('[data-payroll-empty]');
                const estimateEmpty = document.querySelector('[data-estimate-empty]');
                const laborEmpty = document.querySelector('[data-labor-empty]');
                const createProjectSelect = document.querySelector('[data-payroll-project-select]');
                const estimateProjectSelect = document.querySelector('[data-estimate-project-select]');
                const laborDeleteDialog = document.querySelector('[data-labor-delete-dialog]');
                const laborDeleteForm = document.querySelector('[data-labor-delete-form]');
                const laborHistoryLink = document.querySelector('[data-labor-history-link]');
                const laborScopeNote = document.querySelector('[data-labor-scope-note]');
                let pendingDeleteRow = null;
                let pendingDeleteUrl = '';
                let selectedProjectId = @json((string) $selectedProjectId);
                let laborFilter = 'all';

                const applyProjectFilters = () => {
                    const showAllProjects = selectedProjectId === 'all';
                    let visiblePayrolls = 0;

                    payrollRows.forEach((row) => {
                        const visible = showAllProjects || row.dataset.payrollProject === selectedProjectId;
                        row.hidden = !visible;
                        visiblePayrolls += visible ? 1 : 0;
                    });

                    if (payrollEmpty) {
                        payrollEmpty.hidden = visiblePayrolls > 0;
                    }

                    let visibleEstimates = 0;

                    estimateRows.forEach((row) => {
                        const visible = showAllProjects || row.dataset.estimateProject === selectedProjectId;
                        row.hidden = !visible;
                        visibleEstimates += visible ? 1 : 0;
                    });

                    if (estimateEmpty) {
                        estimateEmpty.hidden = visibleEstimates > 0;
                    }

                    let visibleLaborRows = 0;

                    laborRows.forEach((row) => {
                        const matchesProject = showAllProjects || row.dataset.laborProjectId === selectedProjectId;
                        const matchesType = laborFilter === 'all' || row.dataset.laborRow === laborFilter;
                        const visible = matchesProject && matchesType;
                        row.hidden = !visible;
                        visibleLaborRows += visible ? 1 : 0;
                    });

                    if (laborEmpty) {
                        laborEmpty.hidden = visibleLaborRows > 0;
                    }
                };

                projectButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        selectedProjectId = button.dataset.projectId || '';

                        projectButtons.forEach((projectButton) => {
                            const selected = projectButton === button;
                            projectButton.classList.toggle('active', selected);
                            projectButton.setAttribute('aria-pressed', selected ? 'true' : 'false');
                        });

                        selectedProjectNames.forEach((projectName) => {
                            projectName.textContent = button.dataset.projectName || 'Sin obra seleccionada';
                        });

                        if (createProjectSelect && selectedProjectId !== 'all') {
                            createProjectSelect.value = selectedProjectId;
                        }

                        if (estimateProjectSelect && selectedProjectId !== 'all') {
                            estimateProjectSelect.value = selectedProjectId;
                        }

                        if (laborHistoryLink && selectedProjectId !== 'all') {
                            const historyUrl = new URL(laborHistoryLink.dataset.historyBase, window.location.origin);
                            historyUrl.searchParams.set('project', selectedProjectId);
                            laborHistoryLink.href = historyUrl.toString();
                        }

                        if (laborScopeNote) {
                            laborScopeNote.textContent = selectedProjectId === 'all'
                                ? 'Pendientes de pago de todas las obras'
                                : 'Pendientes de pago de la obra seleccionada';
                        }

                        applyProjectFilters();
                    });
                });

                const catalogToggles = [...document.querySelectorAll('[data-labor-catalog-toggle]')];
                const catalogs = [...document.querySelectorAll('[data-labor-catalog]')];

                const setCatalogOpen = (catalogName, open) => {
                    catalogs.forEach((catalog) => {
                        catalog.hidden = !(open && catalog.dataset.laborCatalog === catalogName);
                    });

                    catalogToggles.forEach((toggle) => {
                        const active = open && toggle.dataset.laborCatalogToggle === catalogName;
                        toggle.classList.toggle('is-active', active);
                        toggle.setAttribute('aria-expanded', active ? 'true' : 'false');
                        const indicator = toggle.querySelector('[data-labor-catalog-indicator]');
                        if (indicator) indicator.textContent = active ? '\u2212' : '+';
                    });
                };

                catalogToggles.forEach((toggle) => {
                    toggle.addEventListener('click', () => {
                        const catalogName = toggle.dataset.laborCatalogToggle;
                        const catalog = catalogs.find((item) => item.dataset.laborCatalog === catalogName);
                        setCatalogOpen(catalogName, catalog?.hidden ?? true);
                    });
                });

                document.querySelectorAll('[data-labor-catalog-close]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const catalogName = button.dataset.laborCatalogClose;
                        setCatalogOpen(catalogName, false);
                        document.querySelector(`[data-labor-catalog-toggle="${catalogName}"]`)?.focus();
                    });
                });

                document.querySelectorAll('[data-labor-filter]').forEach((button) => {
                    button.addEventListener('click', () => {
                        laborFilter = button.dataset.laborFilter || 'all';

                        document.querySelectorAll('[data-labor-filter]').forEach((tab) => {
                            tab.classList.toggle('is-active', tab === button);
                        });

                        applyProjectFilters();
                    });
                });

                document.querySelector('[data-payroll-create]')?.addEventListener('click', () => {
                    if (createProjectSelect && selectedProjectId !== 'all') {
                        createProjectSelect.value = selectedProjectId;
                    }
                });

                document.querySelector('[data-estimate-create]')?.addEventListener('click', () => {
                    if (estimateProjectSelect && selectedProjectId !== 'all') {
                        estimateProjectSelect.value = selectedProjectId;
                    }
                });

                document.querySelectorAll('[data-file-picker-target]').forEach((button) => {
                    button.addEventListener('click', () => {
                        document.getElementById(button.dataset.filePickerTarget)?.click();
                    });
                });

                document.querySelectorAll('[data-auto-file-submit]').forEach((input) => {
                    input.addEventListener('change', () => {
                        if (input.files?.length) {
                            input.form?.submit();
                        }
                    });
                });

                const progressPhotoDialogs = [...document.querySelectorAll('[data-progress-photo-dialog]')];

                progressPhotoDialogs.forEach((dialog) => {
                    const form = dialog.querySelector('[data-progress-photo-form]');
                    const selectStage = dialog.querySelector('[data-progress-photo-stage="select"]');
                    const describeStage = dialog.querySelector('[data-progress-photo-stage="describe"]');
                    const title = dialog.querySelector('[data-progress-photo-dialog-title]');
                    const subtitle = dialog.querySelector('[data-progress-photo-dialog-subtitle]');
                    const dropZone = dialog.querySelector('[data-progress-photo-drop-zone]');
                    const picker = dialog.querySelector('[data-progress-photo-picker]');
                    const camera = dialog.querySelector('[data-progress-photo-camera]');
                    const submitFiles = dialog.querySelector('[data-progress-photo-submit-files]');
                    const selectedList = dialog.querySelector('[data-progress-photo-selected-list]');
                    const saveButton = dialog.querySelector('[data-progress-photo-save]');
                    const errorNodes = [...dialog.querySelectorAll('[data-progress-photo-client-error]')];
                    const initialSubtitle = subtitle?.textContent || '';
                    const maxFiles = 10;
                    const maxFileSize = 10 * 1024 * 1024;
                    let selectedPhotos = [];
                    let isSubmitting = false;

                    const setError = (message = '') => {
                        errorNodes.forEach((node) => {
                            node.textContent = message;
                            node.hidden = message === '';
                        });
                    };

                    const setStage = (stage) => {
                        if (selectStage) selectStage.hidden = stage !== 'select';
                        if (describeStage) describeStage.hidden = stage !== 'describe';

                        if (title) {
                            title.textContent = stage === 'describe' ? 'Descripcion de las fotos' : 'Agregar fotos';
                        }

                        if (subtitle) {
                            subtitle.textContent = stage === 'describe'
                                ? `${selectedPhotos.length} ${selectedPhotos.length === 1 ? 'foto seleccionada' : 'fotos seleccionadas'}`
                                : initialSubtitle;
                        }
                    };

                    const syncSubmissionFiles = () => {
                        if (!submitFiles) return false;

                        try {
                            const transfer = new DataTransfer();
                            selectedPhotos.forEach(({ file }) => transfer.items.add(file));
                            submitFiles.files = transfer.files;

                            return true;
                        } catch (error) {
                            setError('No fue posible preparar las fotos. Vuelve a seleccionarlas.');

                            return false;
                        }
                    };

                    const clearSelectedPhotos = () => {
                        selectedPhotos.forEach(({ previewUrl }) => URL.revokeObjectURL(previewUrl));
                        selectedPhotos = [];
                        if (selectedList) selectedList.replaceChildren();
                        if (submitFiles) submitFiles.value = '';
                        if (picker) picker.value = '';
                        if (camera) camera.value = '';
                        if (saveButton) {
                            saveButton.disabled = true;
                            saveButton.textContent = 'Guardar fotos';
                        }
                        setError();
                        setStage('select');
                    };

                    const renderSelectedPhotos = () => {
                        if (!selectedList) return;

                        selectedList.replaceChildren();

                        selectedPhotos.forEach((photo, index) => {
                            const item = document.createElement('article');
                            item.className = 'progress-photo-selected-item';

                            const image = document.createElement('img');
                            image.className = 'progress-photo-selected-preview';
                            image.src = photo.previewUrl;
                            image.alt = `Vista previa de ${photo.file.name}`;

                            const copy = document.createElement('div');
                            copy.className = 'progress-photo-selected-copy';

                            const label = document.createElement('label');
                            label.htmlFor = `${dialog.id}-description-${index}`;
                            label.append('Descripcion ');
                            const requiredMark = document.createElement('span');
                            requiredMark.textContent = '*';
                            requiredMark.setAttribute('aria-hidden', 'true');
                            label.append(requiredMark);

                            const textarea = document.createElement('textarea');
                            textarea.id = `${dialog.id}-description-${index}`;
                            textarea.name = 'progress_descriptions[]';
                            textarea.rows = 3;
                            textarea.maxLength = 500;
                            textarea.required = true;
                            textarea.value = photo.description;
                            textarea.placeholder = 'Describe el avance que aparece en esta foto.';
                            textarea.addEventListener('input', () => {
                                photo.description = textarea.value;
                                setError();
                            });

                            copy.append(label, textarea);

                            const removeButton = document.createElement('button');
                            removeButton.className = 'progress-photo-remove-selected';
                            removeButton.type = 'button';
                            removeButton.textContent = '\u00d7';
                            removeButton.title = 'Quitar foto';
                            removeButton.setAttribute('aria-label', `Quitar ${photo.file.name}`);
                            removeButton.addEventListener('click', () => {
                                URL.revokeObjectURL(photo.previewUrl);
                                selectedPhotos.splice(index, 1);

                                if (selectedPhotos.length === 0) {
                                    clearSelectedPhotos();
                                    return;
                                }

                                renderSelectedPhotos();
                            });

                            item.append(image, copy, removeButton);
                            selectedList.append(item);
                        });

                        if (saveButton) {
                            saveButton.disabled = selectedPhotos.length === 0;
                            saveButton.textContent = selectedPhotos.length === 1
                                ? 'Guardar 1 foto'
                                : `Guardar ${selectedPhotos.length} fotos`;
                        }

                        syncSubmissionFiles();
                        setStage(selectedPhotos.length > 0 ? 'describe' : 'select');
                    };

                    const addFiles = (files) => {
                        const incomingFiles = [...(files || [])];
                        const existingKeys = new Set(selectedPhotos.map(({ file }) => `${file.name}:${file.size}:${file.lastModified}`));
                        let rejectedType = false;
                        let rejectedSize = false;
                        let limitReached = false;

                        incomingFiles.forEach((file) => {
                            if (selectedPhotos.length >= maxFiles) {
                                limitReached = true;
                                return;
                            }

                            const extensionIsValid = /\.(jpe?g|png|webp)$/i.test(file.name);
                            const mimeIsValid = ['image/jpeg', 'image/png', 'image/webp'].includes((file.type || '').toLowerCase());

                            if (!extensionIsValid && !mimeIsValid) {
                                rejectedType = true;
                                return;
                            }

                            if (file.size > maxFileSize) {
                                rejectedSize = true;
                                return;
                            }

                            const key = `${file.name}:${file.size}:${file.lastModified}`;
                            if (existingKeys.has(key)) return;

                            existingKeys.add(key);
                            selectedPhotos.push({
                                file,
                                description: '',
                                previewUrl: URL.createObjectURL(file),
                            });
                        });

                        if (selectedPhotos.length > 0) {
                            renderSelectedPhotos();
                        }

                        if (limitReached) {
                            setError('Puedes guardar hasta 10 fotos en cada carga.');
                        } else if (rejectedSize) {
                            setError('Cada foto debe pesar 10 MB o menos.');
                        } else if (rejectedType) {
                            setError('Selecciona imagenes JPG, PNG o WEBP.');
                        } else {
                            setError();
                        }
                    };

                    dialog.querySelector('[data-progress-photo-select]')?.addEventListener('click', (event) => {
                        event.stopPropagation();
                        picker?.click();
                    });

                    dialog.querySelector('[data-progress-photo-more]')?.addEventListener('click', () => picker?.click());
                    dialog.querySelector('[data-progress-photo-camera-button]')?.addEventListener('click', () => camera?.click());

                    [picker, camera].forEach((input) => {
                        input?.addEventListener('change', () => {
                            addFiles(input.files);
                            input.value = '';
                        });
                    });

                    ['dragenter', 'dragover'].forEach((eventName) => {
                        dropZone?.addEventListener(eventName, (event) => {
                            event.preventDefault();
                            dropZone.classList.add('is-dragging');
                        });
                    });

                    ['dragleave', 'drop'].forEach((eventName) => {
                        dropZone?.addEventListener(eventName, (event) => {
                            event.preventDefault();
                            dropZone.classList.remove('is-dragging');
                        });
                    });

                    dropZone?.addEventListener('drop', (event) => addFiles(event.dataTransfer?.files));
                    dropZone?.addEventListener('click', (event) => {
                        if (!event.target.closest('button')) picker?.click();
                    });
                    dropZone?.addEventListener('keydown', (event) => {
                        if (event.key === 'Enter' || event.key === ' ') {
                            event.preventDefault();
                            picker?.click();
                        }
                    });

                    dialog.querySelectorAll('[data-progress-photo-cancel]').forEach((button) => {
                        button.addEventListener('click', () => dialog.close());
                    });

                    form?.addEventListener('submit', (event) => {
                        setError();

                        if (selectedPhotos.length === 0) {
                            event.preventDefault();
                            setStage('select');
                            setError('Selecciona al menos una foto.');
                            return;
                        }

                        const missingDescription = selectedPhotos.some((photo) => photo.description.trim() === '');
                        if (missingDescription) {
                            event.preventDefault();
                            setError('Agrega una descripcion para cada foto.');
                            selectedList?.querySelector('textarea:invalid')?.focus();
                            return;
                        }

                        if (!syncSubmissionFiles()) {
                            event.preventDefault();
                            return;
                        }

                        isSubmitting = true;
                        if (saveButton) saveButton.disabled = true;
                    });

                    dialog.addEventListener('close', () => {
                        if (isSubmitting) return;

                        clearSelectedPhotos();
                        const returnDialog = document.getElementById(dialog.dataset.returnDialog || '');
                        if (returnDialog && !returnDialog.open) {
                            window.requestAnimationFrame(() => returnDialog.showModal());
                        }
                    });

                    dialog._resetProgressPhotoFlow = clearSelectedPhotos;
                });

                document.querySelectorAll('[data-progress-photo-open]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const dialog = document.getElementById(button.dataset.progressPhotoOpen || '');
                        if (!dialog) return;

                        dialog._resetProgressPhotoFlow?.();
                        button.closest('[data-supply-detail-dialog]')?.close();
                        window.requestAnimationFrame(() => dialog.showModal());
                    });
                });

                document.querySelectorAll('[data-progress-photo-edit-toggle]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const form = button.closest('[data-progress-photo-card]')?.querySelector('[data-progress-photo-edit-form]');
                        if (!form) return;

                        form.hidden = false;
                        form.querySelector('textarea')?.focus();
                    });
                });

                document.querySelectorAll('[data-progress-photo-edit-cancel]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const form = button.closest('[data-progress-photo-edit-form]');
                        if (!form) return;

                        const textarea = form.querySelector('textarea');
                        if (textarea) textarea.value = textarea.defaultValue;
                        form.hidden = true;
                    });
                });

                document.querySelectorAll('[data-progress-photo-delete-form]').forEach((form) => {
                    form.addEventListener('submit', (event) => {
                        if (!window.confirm('Estas seguro que quieres eliminar esta foto de avance?')) {
                            event.preventDefault();
                        }
                    });
                });

                document.querySelectorAll('[data-labor-delete]').forEach((button) => {
                    button.addEventListener('click', () => {
                        pendingDeleteRow = button.closest('[data-labor-row], [data-payroll-row], [data-estimate-row]');
                        pendingDeleteUrl = button.dataset.laborDeleteUrl || '';

                        if (pendingDeleteRow && pendingDeleteUrl && laborDeleteDialog) {
                            laborDeleteDialog.showModal();
                        }
                    });
                });

                const closeLaborDeleteDialog = () => {
                    laborDeleteDialog?.close();
                    pendingDeleteRow = null;
                    pendingDeleteUrl = '';
                };

                document.querySelector('[data-labor-delete-no]')?.addEventListener('click', closeLaborDeleteDialog);

                document.querySelector('[data-labor-delete-yes]')?.addEventListener('click', () => {
                    if (pendingDeleteUrl && laborDeleteForm) {
                        laborDeleteForm.action = pendingDeleteUrl;
                        laborDeleteForm.submit();
                    }
                });

                laborDeleteDialog?.addEventListener('click', (event) => {
                    if (event.target === laborDeleteDialog) {
                        closeLaborDeleteDialog();
                    }
                });

                laborDeleteDialog?.addEventListener('close', () => {
                    pendingDeleteRow = null;
                    pendingDeleteUrl = '';
                });

                applyProjectFilters();

                if (@json(request()->boolean('open_payroll') || $invalidPayrollDialogId === 'new-payroll-dialog')) {
                    setCatalogOpen('payroll', true);
                }

                if (@json(request()->boolean('open_estimates') || $invalidPayrollDialogId === 'new-estimate-dialog')) {
                    setCatalogOpen('estimates', true);
                }

                const invalidPayrollDialogId = @json($invalidPayrollDialogId);
                if (invalidPayrollDialogId) {
                    const invalidDialog = document.getElementById(invalidPayrollDialogId);
                    window.requestAnimationFrame(() => invalidDialog?.showModal());
                }

                const invoiceDialogToOpen = @json(old('invoice_dialog', session('open_invoice_dialog')));
                const invalidProgressPhotoDialogId = @json(old('progress_photo_dialog'));
                if (invalidProgressPhotoDialogId) {
                    const invalidProgressDialog = document.getElementById(invalidProgressPhotoDialogId);
                    window.requestAnimationFrame(() => invalidProgressDialog?.showModal());
                } else if (invoiceDialogToOpen) {
                    const invoiceDialog = document.getElementById(invoiceDialogToOpen);
                    window.requestAnimationFrame(() => invoiceDialog?.showModal());
                }
            })();
        </script>
    </x-app-shell>
@endsection
